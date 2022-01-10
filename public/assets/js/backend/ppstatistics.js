define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppstatistics/index' + location.search,
                    add_url: 'ppstatistics/add',
                    edit_url: 'ppstatistics/edit',
                    del_url: 'ppstatistics/del',
                    multi_url: 'ppstatistics/multi',
                    import_url: 'ppstatistics/import',
                    table: 'ppstatistics',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('id')},
                        // {field: 'ppaccount.ppaccount', title: __('pp账号'), operate: 'like'},

                        {field: 'ppaccount.ppaccount', title: __('pp账号'),formatter:function (value,data) {
                            var html = '<a class="ppaccount" data-account_id='+data.account_id+' style="clore:red;cursor:pointer">'+value+'</a>'
                            return html;
                        }},
                        


                        {field: 'order_qty', title: __('order_qty'), operate: 'BETWEEN',visible:false},
                        {field: 'order_total', title: __('order_total'), operate: 'BETWEEN',visible:false},
                        {field: 'order_total', title: __('总订单量/总金额'),searchable:false, operate: 'BETWEEN',formatter:function (value,data) {
                            var html = data.order_qty+' / '+data.order_total
                            return html;
                        }},
                        {field: 'success_order_qty', title: __('success_order_qty'), operate: 'BETWEEN',visible:false},
                        {field: 'success_total', title: __('success_total'), operate: 'BETWEEN',visible:false},
                        {field: 'success_total', title: __('下单成功金额/订单量'),searchable:false, operate: 'BETWEEN',formatter:function (value,data) {
                            var html = data.success_order_qty+' / '+data.success_total
                            return html;
                        }},
                        // {field: 'error_order_qty', title: __('error_order_qty'), operate: 'BETWEEN'},
                        // {field: 'error_total', title: __('error_total'), operate: 'BETWEEN'},
                        {field: 'unpaid_order_qty', title: __('unpaid_order_qty'), operate: 'BETWEEN',visible:false},
                        {field: 'unpaid_total', title: __('unpaid_total'), operate: 'BETWEEN',visible:false},
                        {field: 'unpaid_total', title: __('未付款订单总金额/总订单量'), searchable:false,operate: 'BETWEEN',formatter:function (value,data) {
                            var html = data.unpaid_order_qty+' / '+data.unpaid_total
                            return html;
                        }},
                        {field: 'refund_total', title: __('退款金额'), operate: 'BETWEEN',visible:false},
                        {field: 'refund_qty', title: __('退款订单量'), operate: 'BETWEEN',visible:false},
                        {field: 'unpaid_total', title: __('退款金额/订单量'),searchable:false, operate: 'BETWEEN',formatter:function (value,data) {
                            var html = data.refund_total+' / '+data.refund_qty
                            return html;
                        }},
                        {field: 'first_order_date', title: __('first_order_time'), operate:'RANGE',  autocomplete:false,addclass:'datetimerange',},
                        {field: 'end_order_date', title: __('end_order_time'),operate:'RANGE',  autocomplete:false,addclass:'datetimerange',},
                        {field: 'success_rate', title: __('成功率'),operate:'RANGE'},
                        {field: 'ppaccount.status', title: __('Status'), searchList: {"on":__('Status on'),"off":__('Status off'),"limited":__('Status limited'),"limited180":__('Status limited180'),"autooff":__('Status Offline')}, formatter: Table.api.formatter.status},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            $(document).on("click", ".ppaccount", function () {
                let account_id = $(this).data('account_id')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: 'http://dev.paymentcc.com/cnLyKYhOfC.php/pporder/index?pp_id='+account_id+'&show_type=true' 
                }); 
                
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
