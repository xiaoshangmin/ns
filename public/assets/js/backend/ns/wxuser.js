define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ns.wxuser/index' + location.search,
                    add_url: 'ns.wxuser/add',
                    edit_url: 'ns.wxuser/edit',
                    del_url: 'ns.wxuser/del',
                    multi_url: 'ns.wxuser/multi',
                    table: 'wxuser',
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
                pk: 'uid',
                sortName: 'uid',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'uid', title: __('Uid')},
                        {field: 'openid', title: __('Openid'),operate: false},
                        // {field: 'unionid', title: __('Unionid')},
                        // {field: 'session_key', title: __('Session_key')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'sex', title: __('Sex'), searchList: {"1":__('Sex 1'),"2":__('Sex 2')}, formatter: Table.api.formatter.normal,operate: false},
                        // {field: 'language', title: __('Language')},
                        // {field: 'country', title: __('Country')},
                        // {field: 'province', title: __('Province')},
                        // {field: 'city', title: __('City')},
                        {field: 'headimgurl', title: __('Headimgurl'),events: Table.api.events.image, formatter: Table.api.formatter.image,operate: false},
                        // {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetimem,operate: false},
                        // {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'ip', title: __('Ip')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}                         
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