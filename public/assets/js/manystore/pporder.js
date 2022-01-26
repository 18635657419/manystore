define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pporder/index' + location.search,
                    add_url: 'pporder/add',
                    edit_url: 'pporder/edit',
                    del_url: 'pporder/del',
                    multi_url: 'pporder/multi',
                    import_url: 'pporder/import',
                    table: 'pporder',
                }
            });

            var table = $("#table");
            let canshu = location.search
            // 根据传递参数判断是否显示头部搜索
            if(canshu.indexOf("show_type") != -1 ){
                   // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'order_id',
                    sortName: 'order_id',
                    searchFormVisible:false,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'order_id', title: __('Order_id')},
                            {field: 'pp_id', title: __('pp_id'),visible:false},
                            {field: 'ordername', title: __('Ordername'), operate: 'LIKE'},
                            {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                            {field: 'status', title: __('Status'), searchList: {"plated":__('Status plated'),"cancal":__('Status cancal'),"ing":__('Status ing'),'pendding':'Pendding'}, formatter: Table.api.formatter.status},
                            {field: 'storename', title: __('Storename'), operate: 'LIKE'},
                            {field: 'proxy_order_id', title: __('Proxy_order_id')},
                            {field: 'proxyemail', title: __('Proxyemail'), operate: 'LIKE'},
                            {field: 'zip', title: __('Zip'), operate: 'LIKE'},
                            {field: 'country', title: __('Country'), operate: 'LIKE'},
                            {field: 'city', title: __('City'), operate: 'LIKE'},
                            {field: 'address', title: __('Address'), operate: 'LIKE'},
                            {field: 'username', title: __('Username'), operate: 'LIKE'},
                            {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                            {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                            {field: 'updatedate', title: __('Updatedate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                            {field: 'ppaccount.ppaccount', title: __('Ppaccount.ppaccount'), operate: 'LIKE'},
                            {field: 'domainmanage.name', title: __('Domainmanage.name'), operate: 'LIKE'},
                            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });
                $(".fixed-table-toolbar").hide()
                // $(".panel-heading").hide()
            }else{
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'order_id',
                    sortName: 'order_id',
                    searchFormVisible:true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'order_id', title: __('Order_id')},
                            {field: 'pp_id', title: __('pp_id'),visible:false},
                            {field: 'ordername', title: __('Ordername'), operate: 'LIKE'},
                            {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                            {field: 'status', title: __('Status'), searchList: {"plated":__('Status plated'),"cancal":__('Status cancal'),"ing":__('Status ing'),'pendding':'Pendding'}, formatter: Table.api.formatter.status},
                            {field: 'storename', title: __('Storename'), operate: 'LIKE'},
                            {field: 'proxy_order_id', title: __('Proxy_order_id')},
                            {field: 'proxyemail', title: __('Proxyemail'), operate: 'LIKE'},
                            {field: 'zip', title: __('Zip'), operate: 'LIKE'},
                            {field: 'country', title: __('Country'), operate: 'LIKE'},
                            {field: 'city', title: __('City'), operate: 'LIKE'},
                            {field: 'address', title: __('Address'), operate: 'LIKE'},
                            {field: 'username', title: __('Username'), operate: 'LIKE'},
                            {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                            {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                            {field: 'updatedate', title: __('Updatedate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                            {field: 'ppaccount.ppaccount', title: __('Ppaccount.ppaccount'), operate: 'LIKE'},
                            {field: 'domainmanage.name', title: __('Domainmanage.name'), operate: 'LIKE'},
                            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });
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
