define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppexpress_block/index' + location.search,
                    add_url: 'ppexpress_block/add',
                    edit_url: 'ppexpress_block/edit',
                    del_url: 'ppexpress_block/del',
                    multi_url: 'ppexpress_block/multi',
                    import_url: 'ppexpress_block/import',
                    table: 'ppexpress_block',
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
                        {field: 'block_number', title: __('批号'), operate: 'like',formatter:function (value,data) {
                            var html = '<a class="ppaccount" data-block_number='+value+' style="clore:red;cursor:pointer">'+value+'</a>'+''
                            return html;
                        }},
                        {field: 'qty', title: __('导入数量'), operate: 'BETWEEN'},
                        {field: 'remark', title: __('备注'), operate: 'like'},
                        {field: 'createdate', title: __('创建时间'), operate:'RANGE',  autocomplete:false,addclass:'datetimerange',},

                     
                    ]
                ]
            });

            $(document).on("click", ".ppaccount", function () {
                let block_number = $(this).data('block_number')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: '/cnLyKYhOfC.php/ppexpress?block_number='+block_number+'&show_type=true' 
                }); 
                
            });

            
            $("#Import").click(function(){
              
                layer.prompt({
                    formType: 2,
                    placeholder: '提示信息',
                    title: '导入格式：订单ID,运单号,快递公司   一行一个账号，多个账号请回车换行',
                    maxlength:10000,
                    area: ['800px', '350px'] //自定义文本域宽高
                  }, function(value, index, elem){
                    // 验证导入数据
                    $.ajax({
                        url: "ppexpress/checkdata",
                        type: "POST",
                        dataType: "json",
                        data: {value:value},
                        success: function (ret) {
                            if(ret.code){
                                layer.prompt({
                                    formType:3 ,
                                    value: '',
                                    title: '请输入备注',
                                    area: ['300px', '100px'] //自定义文本域宽高
                                }, function(remark, index, elem){
                                    $.ajax({
                                        url: "ppexpress/importAccount",
                                        type: "POST",
                                        dataType: "json",
                                        data: {remark:remark,value:value},
                                        success: function (ret) {
                                            if(ret.code){
                                                Toastr.success(ret.msg)
                                                $('#table').bootstrapTable('refresh');
                                                //  跳转到批号管理
                                             
                                                   
                                                layer.closeAll();
                                            }else{
                                                Toastr.error(ret.msg)
                                            }
                                        },
                                        error: function (xhr) {
                                            
                                        }
                                    });
                                    
                                    layer.close(index);
                                });
                               
                            }else{
                                Toastr.error(ret.msg)
                            }
                        },
                        error: function (xhr) {
                            
                        }
                    });

                    return

                     
                  
                    //  layer.close(index);
                  });
            })
           
           

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
