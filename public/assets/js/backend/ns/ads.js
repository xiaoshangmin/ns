define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ns.ads/index' + location.search,
                    add_url: 'ns.ads/add',
                    edit_url: 'ns.ads/edit',
                    del_url: 'ns.ads/del',
                    multi_url: 'ns.ads/multi',
                    table: 'ads',
                }
            });
            
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                commonSearch: false,
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'title', title: __('Title')},
                        {field: 'picture', title: __('Picture'),events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'link_type', title: __('Link_type'), searchList: {"1":__('Link_type 1'),"2":__('Link_type 2'),"3":__('Link_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'link_info', title: __('Link_info')},
                        // {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'sort', title: __('Sort')},
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
            var row = Table.list
            console.log(row)
        },
        api: {
            bindevent: function () {
                $('#c-link_type').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                    if(1 == e.target.value){
                        $('#link1').show();
                        $('#link2').hide();
                        $('#link3').hide();
                    }else if(2 == e.target.value){
                        $('#link1').hide();
                        $('#link2').show();
                        $('#link3').hide();
                    }else if(3 == e.target.value){
                        $('#link1').hide();
                        $('#link2').hide();
                        $('#link3').show();
                    }
                  });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});