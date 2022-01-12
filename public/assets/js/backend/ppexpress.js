define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppexpress/index' + location.search,
                    add_url: 'ppexpress/add',
                    edit_url: 'ppexpress/edit',
                    del_url: 'ppexpress/del',
                    multi_url: 'ppexpress/multi',
                    import_url: 'ppexpress/import',
                    table: 'ppexpress',
                }
            });

            var table = $("#table");
            let canshu = location.search
            let show_type = true
            if(canshu.indexOf("show_type") != -1 ){
              show_type = false
              $(".fixed-table-toolbar").hide()
              $("#Import").hide()
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
                        {field: 'id', title: __('id')},
                        {field: 'pporder.proxy_order_id', title: __('网站订单'), operate: 'like'},
                        {field: 'pporder.proxyemail', title: __('客户邮箱'), operate: 'like'},
                        {field: 'express_number', title: __('运单号'), operate: 'LIKE'},
                        {field: 'exress_name', title: __('快递公司'), operate: 'LIKE'},
                        {field: 'block_number', title: __('批号'), operate: 'like'},
                        {field: 'remark', title: __('备注'), operate: 'LIKE'},
                        {field: 'status', title: __('状态'), operate: 'like', searchList: {"unexecuted":__('未执行'),'implement':__('已执行')}, formatter: Table.api.formatter.status},
                        {field: 'createdate', title: __('创建时间'), operate:'RANGE',  autocomplete:false,addclass:'datetimerange',},
                    ]
                ]
            });

            
            if(canshu.indexOf("show_type") != -1 ){
                $(".fixed-table-toolbar").hide()
                $("#Import").hide()
              }
            // $(document).on("click", ".ppaccount", function () {
            //     let block_number = $(this).data('block_number')
            //     // alert(ppaccount);
            //     layer.open({
            //         area:["90%","90%"],
            //         type: 2, 
            //         content: '/cnLyKYhOfC.php/ppaccount?block_number='+block_number+'&show_type=true' 
            //     }); 
                
            // });

            $("#Import").click(function(){
               
                layer.prompt({
                    formType: 2,
                    placeholder: '提示信息',
                    title: '导入格式：网站订单ID,运单号,快递公司   一行一个账号，多个账号请回车换行',
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
                                let optionedata = ret.data
                                let optione = ''
                                for (var i=0;i<optionedata.length;i++)
                                { 
                                    optione +=  '<option value="'+optionedata[i].pp_id+'">'+optionedata[i].ppaccount+'</option>'
                                }
                                console.log(optionedata)
                                console.log(optione)
                                let selectHtml = '<div class="form-group ">'+
                              
                                '<div class="col-xs-12 col-sm-8">'+
                                  '<label for="gender" class="control-label ">pp账号:</label>'+
                                    '<select id="pp_id" data-rule="required" class="form-control selectpicker" name="pp_id">'+optione+
                                   
                                    '</select>'+
                                    "<input style='margin: 10px 0px;width:100%' id='remark' type='text' class='layui-layer-input' value='' placeholder='备注'>"+
                                '</div>'+
                            '</div>'
                            let content = "<div>"+selectHtml+
                           
                        
                            "</div>"
                            layer.open({
                                type: 1 //Page层类型
                                ,btn:["确定","取消"]
                                ,area: ['470px', '270px'] //自定义文本域宽高
                                ,title: '请选择pp账号'
                                ,skin: 'layui-layer-prompt'
                                ,content: content
                                ,yes: function(index, layero){
                                  //按钮【按钮一】的回调
                                    let remark = $(layero).find("#remark").val()
                                    let pp_id = $("#pp_id").val();
                                    $.ajax({
                                        url: "ppexpress/importAccount",
                                        type: "POST",
                                        dataType: "json",
                                        data: {remark:remark,pp_id:pp_id,value:value},
                                        success: function (ret) {
                                            if(ret.code){
                                                Toastr.success(ret.msg)
                                                // $('#table').bootstrapTable('refresh');
                                                //  跳转到批号管理
                                             
                                                setTimeout( Backend.api.addtabs('/cnLyKYhOfC.php/ppblock?ref=addtabs',{iframeForceRefresh: true}),"20000");
                                                   
                                                layer.closeAll();
                                            }else{
                                                Toastr.error(ret.msg)
                                            }
                                        },
                                        error: function (xhr) {
                                            
                                        }
                                    });
                                    // console.log( $('input:radio[name="status"]:checked').val())
                                    // console.log($(layero).find("#remark").val());
                                    // console.log($(layero).find("#offline_day_value").val());
                                    // console.log($(layero).find("#block_remarks").val());
                                }
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
