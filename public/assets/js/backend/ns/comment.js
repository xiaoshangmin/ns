define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            var cid = Fast.api.query("ids");
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ns.comment/index' + location.search,
                    add_url: 'ns.comment/add',
                    // edit_url: 'ns.comment/edit',
                    del_url: 'ns.comment/del',
                    multi_url: 'ns.comment/multi',
                    table: 'comment',
                },
                queryParams: function (params) { //自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {}; //判断当前是否还有其他高级搜索栏的条件
                    var op = params.op ? JSON.parse(params.op) : {};  //并将搜索过滤器 转为对象方便我们追加条件
                    console.log('cid',cid)
                    if(cid){
                        filter.cid = cid;     //将需要的参数group_id 加入到搜索条件中去 要求url传递的参数必须为group_id=XX;可以用 Fast.api.query("group_id")获取到！
                        op.cid = "=";  //group_id的操作方法的为 找到相等的
                    }
                    params.filter = JSON.stringify(filter); //将搜索过滤器和操作方法 都转为JSON字符串
                    params.op = JSON.stringify(op);
                    //如果希望忽略搜索栏搜索条件,可使用
                    // params.filter = JSON.stringify({url: 'login'});
                    // params.op = JSON.stringify({url: 'like'});
                    return params;

                },
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                commonSearch: false,
                escape: false,
                url: $.fn.bootstrapTable.defaults.extend.index_url + "&cid="+Config.ids.cid,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'cid', title: __('Cid')},
                        // {field: 'pid', title: __('Pid')},
                        // {field: 'uid', title: __('Uid')},
                        {field: 'content', title: __('Content')},
                        // {field: 'pictures', title: __('Pictures')},
                        // {field: 'like_count', title: __('Like_count')},
                        // {field: 'replay_count', title: __('Replay_count')},
                        // {field: 'delete_time', title: __('Delete_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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