define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ns.content/index' + location.search,
                    add_url: 'ns.content/add',
                    edit_url: 'ns.content/edit',
                    del_url: 'ns.content/del',
                    multi_url: 'ns.content/multi',
                    table: 'content',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'uid', title: __('Uid')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'contacts', title: __('Contacts')},
                        {field: 'pictures', title: __('Pictures'),events: Table.api.events.image, formatter: Table.api.formatter.images,operate: false},
                        {field: 'like_count', title: __('Like_count'),operate: false},
                        // {field: 'share_count', title: __('Share_count')},
                        // {field: 'comment_count', title: __('Comment_count')},
                        {field: 'view_count', title: __('View_count'),operate: false},
                        {field: 'address', title: __('Address'),operate: false},
                        // {field: 'lng', title: __('Lng'), operate:'BETWEEN'},
                        // {field: 'lat', title: __('Lat'), operate:'BETWEEN'},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'expiry_time', title: __('Expiry_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'top', title: __('Top'), searchList: {"0":__('Top 0'),"1":__('Top 1')}, formatter: Table.api.formatter.normal},
                        {field: 'pay_status', title: __('Pay_status'), searchList: {"0":__('Pay_status 0'),"1":__('Pay_status 1'),"2":__('Pay_status 2')}, formatter: Table.api.formatter.status},
                        {field: 'is_online', title: __('Is_online'), searchList: {"0":__('Is_online 0'),"1":__('Is_online 1')}, formatter: Table.api.formatter.status},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            width: "150px",
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [ 
                                {
                                    name: 'refresh',
                                    title: __('刷新内容发布时间'),
                                    classname: 'btn btn-xs btn-primary btn-magic btn-ajax',
                                    icon: 'fa fa-refresh',
                                    url: 'ns.content/refresh',
                                    confirm:'确认刷新发布时间？',
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh', {});
                                        //如果需要阻止成功提示，则必须使用return false;
                                        return false;
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'commentList',
                                    title: __('评论列表'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-comment',
                                    url: 'ns.comment/index',
                                },
                                 
                            ],
                            formatter: Table.api.formatter.operate
                        },
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});