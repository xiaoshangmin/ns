<?php
/**
 *  ==============================================================
 *  Created by PhpStorm.
 *  User: Ice
 *  邮箱: ice@sbing.vip
 *  网址: https://sbing.vip
 *  Date: 2020/7/7 下午4:07
 *  ==============================================================
 */

namespace addons\cloudstore\library\qcloud;


use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use Qcloud\Cos\Client;

class QcloudAdapter extends AbstractAdapter  implements AdapterInterface
{
    use NotSupportingVisibilityTrait;

    /**
     * @var \Qcloud\Cos\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $secretId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $region;

    /**
     * QcloudAdapter constructor
     *
     * @param  array  $region
     */
    public function __construct($config)
    {
        $this->region    = $config['region'];
        $this->secretId  = $config['secretId'];
        $this->secretKey = $config['secretKey'];
        $this->bucket    = $config['bucket'];
    }

    /**
     * Write a new file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  Config  $config  Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        $config = new Config(['key' => $path, 'body' => $contents]);

        $options = $this->getOptions($config);

        return $this->client()->putObject($options);
    }

    /**
     * Write a new file using a stream.
     *
     * @param  string    $path
     * @param  resource  $resource
     * @param  Config    $config  Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        $config = new Config(['key' => $path, 'body' => $resource]);

        $options = $this->getOptions($config);

        return $this->client()->putObject($options);
    }

    /**
     * Update a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  Config  $config  Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * Update a file using a stream.
     *
     * @param  string    $path
     * @param  resource  $resource
     * @param  Config    $config  Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param  string  $path
     * @param  string  $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $this->copy($path, $newpath);

        return $this->delete($path);
    }

    /**
     * Copy a file.
     *
     * @param  string  $path
     * @param  string  $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $config = new Config([
            'key' => $newpath, 'copySource' => "{$this->bucket}.cos.{$this->region}.myqcloud.com/{$path}",
        ]);

        $options = $this->getOptions($config);

        return $this->client()->copyObject($options);
    }

    /**
     * Delete a file.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $config = new Config(['key' => $path]);

        $options = $this->getOptions($config);

        return (bool) $this->client()->deleteObject($options);
    }

    /**
     * Delete a directory.
     *
     * @param  string  $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return true;
    }

    /**
     * Create a directory.
     *
     * @param  string  $dirname  directory name
     * @param  Config  $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return ['path' => $dirname, 'type' => 'dir'];
    }

    /**
     * Check whether a file exists.
     *
     * @param  string  $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        try {
            return (bool) $this->getMetaData($path);
        } catch (NoSuchKeyException $e) {
            return false;
        }
    }

    /**
     * Read a file.
     *
     * @param  string  $path
     *
     * @return array|false
     */
    public function read($path)
    {
        try {
            $config = new Config(['key' => $path]);

            $options = $this->getOptions($config);

            $object = $this->client()->getObject($options);

            return ['contents' => (string) $object->get('Body')];
        } catch (NoSuchKeyException $e) {
            return null;
        }
    }

    /**
     * Read a file as a stream.
     *
     * @param  string  $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        if (ini_get('allow_url_fopen')) {
            $stream = fopen('https://'.$this->bucket.'.cos.'.$this->region.'.myqcloud.com/'.$path, 'r');

            return compact("stream", "path");
        }

        return false;
    }

    /**
     * List contents of a directory.
     *
     * @param  string  $directory
     * @param  bool    $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $config = new Config(['directory' => $directory]);

        $options = $this->getOptions($config);

        $object = $this->client()->listObjects($options);

        $contents = (array) $object->get('Contents');

        foreach ($contents as &$content) {
            $content['path'] = $content['Key'];
        }

        return $contents;
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param $path
     *
     * @return array
     */
    public function getMetaData($path)
    {
        $config = new Config(['key' => $path]);

        $options = $this->getOptions($config);

        return $this->client()->headObject($options)->toArray();
    }

    /**
     * Get the size of a file.
     *
     * @param  string  $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        $result = $this->getMetaData($path);

        $size = $result['ContentLength'];

        return compact("size");
    }

    /**
     * Get the mimetype of a file.
     *
     * @param  string  $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $result = $this->getMetaData($path);

        $mimetype = $result['ContentType'];

        return compact("mimetype");
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param  string  $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        $result = $this->getMetaData($path);

        $timestamp = $result['LastModified'];

        return compact("timestamp");
    }

    /**
     * Get a new client.
     *
     * @return \Qcloud\Cos\Client
     */
    public function client()
    {
        return $this->client ?: $this->client = new Client([
            'region'      => $this->region,
            'credentials' => [
                'secretId'  => $this->secretId,
                'secretKey' => $this->secretKey,
            ],
        ]);
    }

    /**
     * Get the setting config
     *
     * @param  Config  $config
     *
     * @return array
     */
    public function getOptions(Config $config)
    {
        $bucket = $this->getBucket();

        $key = $config->get('key');

        $body = $config->get('body');

        $copySource = $config->get('copySource');

        $directory = $config->get('directory');

        return array_filter([
            'Bucket' => $bucket, 'Key' => $key, 'Body' => $body, 'CopySource' => $copySource, 'Prefix' => $directory,
        ]);
    }

    /**
     * Return the bucket
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Return the region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }
}