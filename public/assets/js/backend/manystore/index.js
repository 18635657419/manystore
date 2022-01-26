define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'manystore/index/index',
                    add_url: 'manystore/index/add',
                    edit_url: 'manystore/index/edit',
                    del_url: 'manystore/index/del',
                    multi_url: 'manystore/index/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'operate', title: __('统计信息'),formatter:function (value,data) {
                            var html = '<a class="order btn btn-success" data-shop_id='+data.id+' style="clore:red;cursor:pointer">查看订单统计</a>'+''
                            return html;
                        }},
                        {field: 'id', title: 'ID'},
                        {field: 'ppaccount', title: __('pp账号数量'),formatter:function (value,data) {
                            var html = '<a class="ppaccount" data-shop_id='+data.id+' style="clore:red;cursor:pointer">'+value+'</a>'+''
                            return html;
                        }},
                        {field: 'stripecount', title: __('stripe账号数量'),formatter:function (value,data) {
                            var html = '<a class="stripecount" data-shop_id='+data.id+' style="clore:red;cursor:pointer">'+value+'</a>'+''
                            return html;
                        }},
                        {field: 'username', title: __('Username')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'email', title: __('Email')},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        {field: 'logintime', title: __('Login time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }}
                    ]
                ]
            });
            $(document).on("click", ".order", function () {
                let shop_id = $(this).data('shop_id')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: '/cnLyKYhOfC.php/dashboard?shop_id='+shop_id
                }); 
                
            });

            $(document).on("click", ".ppaccount", function () {
                let shop_id = $(this).data('shop_id')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: '/cnLyKYhOfC.php/ppaccount?shop_id='+shop_id+'&show_type=true' 
                }); 
                
            });
            $(document).on("click", ".stripecount", function () {
                let shop_id = $(this).data('shop_id')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: '/cnLyKYhOfC.php/stripe?shop_id='+shop_id+'&show_type=true' 
                }); 
                
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-address_city").on("cp:updated", function() {
                var citypicker = $(this).data("citypicker");
                var province = citypicker.getCode("province");
                var city = citypicker.getCode("city");
                var district = citypicker.getCode("district");
                if(province){
                    $("#province").val(province);
                }
                if(city){
                    $("#city").val(city);
                }
                if(district){
                    $("#district").val(district);
                }
                $(this).blur();
            });
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            $("#c-address_city").on("cp:updated", function() {
                var citypicker = $(this).data("citypicker");
                var province = citypicker.getCode("province");
                var city = citypicker.getCode("city");
                var district = citypicker.getCode("district");
                if(province){
                    $("#province").val(province);
                }
                if(city){
                    $("#city").val(city);
                }
                if(district){
                    $("#district").val(district);
                }
            });
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
