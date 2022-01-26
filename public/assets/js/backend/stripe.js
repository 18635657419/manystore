define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stripe/index' + location.search,
                    add_url: 'stripe/add',
                    edit_url: 'stripe/edit',
                    del_url: 'stripe/del',
                    multi_url: 'stripe/multi',
                    import_url: 'stripe/import',
                    table: 'stripe',
                }
            });

            var table = $("#table");
            let canshu = location.search
            let show_type = true
            if(canshu.indexOf("show_type") != -1 ){
              show_type = false
              $(".fixed-table-toolbar").hide()
            }

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible:show_type,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'shop_id', title: __('商戶id'),visible:false,},
                        {field: 'manystore.nickname', title: __('商户名称'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        // {field: 'sk_test', title: __('Sk_test'), operate: 'LIKE'},
                        // {field: 'pk_test', title: __('Pk_test'), operate: 'LIKE'},
                        // {field: 'sk_live', title: __('Sk_live'), operate: 'LIKE'},
                        // {field: 'pk_live', title: __('Pk_live'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('Status on'),"pause":__('Status pause'),"off":__('Status off')}, formatter: Table.api.formatter.status},
                        {field: 'totalorder', title: __('Totalorder'), operate:'BETWEEN'},
                        {field: 'totalamount', title: __('Totalamount'), operate:'BETWEEN'},
                        {field: 'orderbyday', title: __('Orderbyday'), operate:'BETWEEN'},
                        {field: 'amountbyday', title: __('Amountbyday'), operate:'BETWEEN'},
                        {field: 'allamount', title: __('总收入'), operate:'BETWEEN'},
                        {field: 'allqty', title: __('总收入订单量'), operate:'BETWEEN'},
                        {field: 'todayamount', title: __('今日收入'), operate:'BETWEEN'},
                        {field: 'todayqty', title: __('今日收入订单量'), operate:'BETWEEN'},
                        {field: 'account_type', title: __('Account_type'), searchList: {"T7":__('Account_type t7'),"T2":__('Account_type t2')}, formatter: Table.api.formatter.normal},
                        {field: 'fail_count', title: __('Fail_count')},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'updatedate', title: __('Updatedate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            if(canshu.indexOf("show_type") != -1 ){
                $(".fixed-table-toolbar").hide()
            }

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