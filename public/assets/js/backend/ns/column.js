define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form,Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ns.column/index' + location.search,
                    add_url: 'ns.column/add',
                    edit_url: 'ns.column/edit',
                    del_url: 'ns.column/del',
                    multi_url: 'ns.column/multi',
                    table: 'column',
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
                escape: false,
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'name', title: __('Name'),formatter: Controller.api.formatter.name},
                        // {field: 'pid', title: __('Pid'),operate: false},
                        {field: 'icon', title: __('Icon'),events: Table.api.events.image, formatter: Table.api.formatter.image,operate: false},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',operate: false},
                        {field: 'refresh_price', title: __('Refresh_price'), operate:'BETWEEN',operate: false},
                        // {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'delete_time', title: __('Delete_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'sort', title: __('Sort'),operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {
                            field: '',
                            title: '<a href="javascript:;" class="btn btn-success btn-xs btn-toggle"><i class="fa fa-chevron-up"></i></a>',
                            operate: false,
                            formatter: Controller.api.formatter.subnode
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //当内容渲染完成后
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                //默认隐藏所有子节点
                //$("a.btn[data-id][data-pid][data-pid!=0]").closest("tr").hide();
                $(".btn-node-sub.disabled").closest("tr").hide();

                //显示隐藏子节点
                $(".btn-node-sub").off("click").on("click", function (e) {
                    var status = $(this).data("shown") ? true : false;
                    $("a.btn[data-pid='" + $(this).data("id") + "']").each(function () {
                        $(this).closest("tr").toggle(!status);
                    });
                    $(this).data("shown", !status);
                    return false;
                });
                //点击切换/排序/删除操作后刷新左侧菜单
                $(".btn-change[data-id],.btn-delone,.btn-dragsort").data("success", function (data, ret) {
                    Fast.api.refreshmenu();
                    return false;
                });

            });
            //批量删除后的回调
            $(".toolbar > .btn-del,.toolbar .btn-more~ul>li>a").data("success", function (e) {
                Fast.api.refreshmenu();
            });
            //展开隐藏一级
            $(document.body).on("click", ".btn-toggle", function (e) {
                $("a.btn[data-id][data-pid][data-pid!=0].disabled").closest("tr").hide();
                var that = this;
                var show = $("i", that).hasClass("fa-chevron-down");
                $("i", that).toggleClass("fa-chevron-down", !show);
                $("i", that).toggleClass("fa-chevron-up", show);
                $("a.btn[data-id][data-pid][data-pid!=0]").not('.disabled').closest("tr").toggle(show);
                $(".btn-node-sub[data-pid=0]").data("shown", show);
            });
            //展开隐藏全部
            $(document.body).on("click", ".btn-toggle-all", function (e) {
                var that = this;
                var show = $("i", that).hasClass("fa-plus");
                $("i", that).toggleClass("fa-plus", !show);
                $("i", that).toggleClass("fa-minus", show);
                $(".btn-node-sub.disabled").closest("tr").toggle(show);
                $(".btn-node-sub").data("shown", show);
            });

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
            if($('#pid').val() > 0){
                $('#price').hide();
                $('#refresh_price').hide();
            }
        },
        api: {
            formatter: {
                name: function (value, row, index) {
                    return row.pid > 0 ? "<span class='text-muted'>" + value + "</span>" : value;
                },
                image: function (value, row, index) {
                    return '<span class="' + ( row.pid > 0 ? 'text-muted' : '') + '"><i class="' + value + '"></i></span>';
                },
                subnode: function (value, row, index) {
                    return '<a href="javascript:;" data-toggle="tooltip" title="' + __('Toggle sub menu') + '" data-id="' + row.id + '" data-pid="' + row.pid + '" class="btn btn-xs '
                        + (row.pid == 0 ? 'btn-success' : 'btn-default disabled') + ' btn-node-sub"><i class="fa fa-sitemap"></i></a>';
                }
            },
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
                
                $('#pid').change(function(){
                    if($(this).val() > 0){
                        $('#price').hide();
                        $('#refresh_price').hide();
                    }else{
                        $('#price').show();
                        $('#refresh_price').show();
                    }
                })
            }
        }
    };
    return Controller;
});