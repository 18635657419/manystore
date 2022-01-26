define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppaccount/index' + location.search,
                    add_url: 'ppaccount/add',
                    edit_url: 'ppaccount/edit',
                    del_url: 'ppaccount/del',
                    multi_url: 'ppaccount/multi',
                    import_url: 'ppaccount/import',
                    table: 'ppaccount',
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
                pk: 'pp_id',
                sortName: 'pp_id',
                searchFormVisible:show_type,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'pp_id', title: __('Pp_id')},
                        {field: 'shop_id', title: __('商戶id'),visible:false,},
                        {field: 'manystore.nickname', title: __('商户昵称'), operate: 'LIKE'},
                        {field: 'manystore.type', title: __('商户类型'), searchList: {"autarky":__('自营'),"other":__('第三方')},formatter: Table.api.formatter.status },
                        {field: 'ppaccount', title: __('Ppaccount'), operate: 'LIKE'},
                        {field: 'b_domain', title: __('B站域名'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('正在使用'),"off":__('待使用'),"limited":__('Status limited'),"limited180":__('不可用'),"autooff":__('提前下线'),'finish':'已完成'}, formatter: Table.api.formatter.status,custom:{'待使用': 'success','finish':'olive','limited180':'warning'}},
                        {field: 'second_status', title: __('二级状态'), searchList: {"on_normal_on":__('正常（审核完成）'),"on_normal_not":__('正常（未出现审核）'),"on_power_ing":__('有收款权限审核中'),"on_power_off":__('有收款权限待提审'),"off_normal":__('待使用'),"autooff_first_off_power":__('首单无收款权限'),"autooff_ing_off_power":__('使用中无收款受限'),"autooff_off_off_power":__('未进单无收款权限'),"autooff_continuity_off":__('连续失败(待人工排查)'),"limited180_money_time":__('不可用'),"finish_check_pending":__('待提审'),"finish_cash_pending":__('待提现'),"finish_on":__('已完成')}, formatter: Table.api.formatter.status,custom:{'off_normal': 'success','finish_on':'olive','limited180_money_time':'warning'}},
                        {field: 'priority', title: __('优先级'), searchList: {"level1":__('最高'),"level2":__('中等'),"level3":__('一般')}, formatter: Table.api.formatter.status},
                        {field: 'totalorder', title: __('Totalorder'), operate:'BETWEEN'},
                        {field: 'totalamount', title: __('Totalamount'), operate:'BETWEEN'},
                        {field: 'orderbyday', title: __('Orderbyday'), operate:'BETWEEN'},
                        {field: 'amountbyday', title: __('Amountbyday'), operate:'BETWEEN'},
                        {field: 'allamount', title: __('总收入'), operate:'BETWEEN'},
                        {field: 'allqty', title: __('总收入订单量'), operate:'BETWEEN'},
                        {field: 'todayamount', title: __('今日收入'), operate:'BETWEEN'},
                        {field: 'todayqty', title: __('今日收入订单量'), operate:'BETWEEN'},
                        {field: 'fail_count', title: __('付款失败次数'), operate:'BETWEEN'},
                        {field: 'block_number', title: __('导入批号'), operate:'LIKE'},
                        {field: 'remark', title: __('remark'), operate:'LIKE'},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'domainmanage.domain_id', title: __('domain_id'),visible:true,},
                        {field: 'domainmanage.name', title: __('域名'),operate:'LIKE'},
                        {field: 'offline_day_value', title: __('下线天数配置'),searchable:false,},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            if(canshu.indexOf("show_type") != -1 ){
                $(".fixed-table-toolbar").hide()
            }

            $("#Import").click(function(){
               
                layer.prompt({
                    formType: 2,
                    placeholder: '提示信息',
                    title: '导入格式： pp账号,域名,公钥,私钥,账号|密码,ip端口  一行一个账号，多个账号请回车换行',
                    maxlength:10000,
                    area: ['800px', '350px'] //自定义文本域宽高
                  }, function(value, index, elem){
                    // 验证导入数据
                    $.ajax({
                        url: "ppaccount/checkdata",
                        type: "POST",
                        dataType: "json",
                        data: {value:value},
                        success: function (ret) {
                            if(ret.code){
                                let optionedata = ret.data.optionedata
                                let optione = ''
                                for (var i=0;i<optionedata.length;i++)
                                { 
                                    optione +=  '<option value="'+optionedata[i].domain_id+'">'+optionedata[i].name+'</option>'
                                }
                                // console.log(optionedata)
                                // console.log(optione)
                                let statusList = ret.data.statusList
                                let optionstatusList = ''
                                Object.keys(statusList).forEach(function(key){

                                    // console.log(key,statusList[key]);
                                    optionstatusList +=  '<label for="status-'+key+'"><input id="status-'+key+'" name="status"  type="radio" value="'+key+'" checked=""> '+statusList[key]+'</label> '
                               
                                });

                                // let secondstatusList = ret.data.secondstatusList
                                // let optionssecondtatusList = ''
                                // Object.keys(secondstatusList).forEach(function(key){

                                //     // console.log(key,statusList[key]);
                                //     optionssecondtatusList +=  '<label for="status-'+key+'"><input id="status-'+key+'" name="second_status"  type="radio" value="'+key+'" checked=""> '+secondstatusList[key]+'</label> '
                               
                                // });


                               
                               

                                // console.log(optionstatusList);



                                let selectHtml = '<div class="form-group col-xs-12 col-sm-8">'+
                              
                                '<div class="col-xs-12 col-sm-8">'+
                                  '<label for="gender" class="control-label ">域名id:</label>'+
                                    '<select id="domain_id" data-rule="required" class="form-control selectpicker" name="domain_id">'+optione+
                                   
                                    '</select>'+
                                '</div>'+
                            '</div>'
                                let content = "<div>"+selectHtml+
                                '<div class="col-xs-12 col-sm-12">'+
                                    '<div class="radio">'+
                                            '<label>状态:</label>'+optionstatusList+
                                            // '<label for="row[status]-on"><input  name="status"  type="radio" value="on" checked=""> 正在使用</label> '+
                                            // '<label for="row[status]-off"><input  name="status"  type="radio" value="off"> 待使用</label> '+
                                            // '<label for="row[status]-limited180"><input name="status" type="radio" value="limited180"> 不可用</label> '+
                                            // '<label for="row[status]-autooff"><input  name="status" type="radio" value="autooff"> 提前下线</label> '+
                                            // '<label for="row[status]-finish"><input name="status"  type="radio" value="finish"> 已完成</label> '+
                                    '</div>'+
                                    // '<div style="margin-top:10px" class="radio">'+
                                    //         '<label>二级状态:</label>'+optionssecondtatusList+
                                            // '<label for="row[second_status]-on"><input  name="second_status"  type="radio" value="on" checked=""> 正在使用</label> '+
                                            // '<label for="row[second_status]-off"><input  name="second_status"  type="radio" value="off"> 待使用</label> '+
                                            // '<label for="row[second_status]-limited180"><input name="second_status" type="radio" value="limited180"> 不可用</label> '+
                                            // '<label for="row[second_status]-autooff"><input  name="second_status" type="radio" value="autooff"> 提前下线</label> '+
                                            // '<label for="row[second_status]-finish"><input name="second_status"  type="radio" value="finish"> 已完成</label> '+
                                    // '</div>'+
                                    
                                '</div>'+
                                "<input style='margin: 10px 32px;' id='remark' type='text' class='layui-layer-input' value='' placeholder='备注'>"+
                                "<input type='number' style='margin: 10px 32px;' id='offline_day_value' type='text' class='layui-layer-input' value='' placeholder='过期天数'>"+
                                "<input type='number' style='margin: 10px 32px;' id='totalorder' type='text' class='layui-layer-input' value='' placeholder='总订单'>"+
                                "<input type='number' style='margin: 10px 32px;' id='totalamount' type='text' class='layui-layer-input' value='' placeholder='总金额'>"+
                                "<input type='number' style='margin: 10px 32px;' id='orderbyday' type='text' class='layui-layer-input' value='' placeholder='每天总订单'>"+
                                "<input type='number' style='margin: 10px 32px;' id='amountbyday' type='text' class='layui-layer-input' value='' placeholder='每天总金额'>"+
                                "</div>"
                            layer.open({
                                type: 1 //Page层类型
                                ,btn:["确定","取消"]
                                ,area: ['670px', '650px'] //自定义文本域宽高
                                ,title: '导入信息确认'
                                ,skin: 'layui-layer-prompt'
                                ,content: content
                                ,yes: function(index, layero){
                                  //按钮【按钮一】的回调
                                    let status = $('input:radio[name="status"]:checked').val()
                                    let remark = $(layero).find("#remark").val()
                                    let offline_day_value = $(layero).find("#offline_day_value").val()
                                    let block_remarks = $(layero).find("#block_remarks").val()
                                    let domain_id = $("#domain_id").val();
                                    let totalorder = $("#totalorder").val();
                                    let totalamount = $("#totalamount").val();
                                    let orderbyday = $("#orderbyday").val();
                                    let amountbyday = $("#amountbyday").val();
                                  


                                    $.ajax({
                                        url: "ppaccount/importAccount",
                                        type: "POST",
                                        dataType: "json",
                                        data: {amountbyday:amountbyday,orderbyday:orderbyday,totalamount:totalamount,totalorder:totalorder,domain_id:domain_id,status:status,remark:remark,offline_day_value:offline_day_value,block_remarks:block_remarks,value:value},
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


            $("#yijistatus li").click(function(){
                var val = $(this).find('a').data('value');
                if(val == 'on'){
                    $("#onstatustab").show();
                    $("#offstatustab").hide();
                    $("#limitedstatustab").hide();
                    $("#autooffstatustab").hide();
                    $("#finishstatustab").hide();
                }else if(val == 'off'){
                    $("#offstatustab").show();
                    $("#onstatustab").hide();
                    $("#limitedstatustab").hide();
                    $("#autooffstatustab").hide();
                    $("#finishstatustab").hide();
                }else if(val == 'limited180'){
                    $("#limitedstatustab").show();
                    $("#offstatustab").hide();
                    $("#onstatustab").hide();
                    $("#autooffstatustab").hide();
                    $("#finishstatustab").hide();
                }else if(val == 'autooff'){
                    $("#autooffstatustab").show();
                    $("#offstatustab").hide();
                    $("#onstatustab").hide();
                    $("#limitedstatustab").hide();
                    $("#finishstatustab").hide();
                }else if(val == 'finish'){
                    $("#finishstatustab").show();
                    $("#offstatustab").hide();
                    $("#onstatustab").hide();
                    $("#limitedstatustab").hide();
                    $("#autooffstatustab").hide();
                }else{
                    // $("#finishstatustab").hide();
                    // $("#offstatustab").hide();
                    // $("#onstatustab").hide();
                    // $("#limitedstatustab").hide();
                    // $("#autooffstatustab").hide();
                }
               
            })

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
       
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            let status_val = $('input:radio[name="row[status]"]:checked').val()

            if(status_val == 'on'){
                $("#onstatus").show();
            }else if(status_val == 'off'){
                $("#offstatus").show();
            }else if(status_val == 'limited180'){
                $("#limitedstatus").show();
            }else if(status_val == 'autooff'){
                $("#autooffstatus").show();
            }else if(status_val == 'finish'){
                $("#finishstatus").show();
            }

            $('input:radio[name="row[status]"]').change(function(){
                let val = $(this).val()
                if(val == 'on'){
                    $("#onstatus").show();
                    $("#offstatus").hide();
                    $("#limitedstatus").hide();
                    $("#autooffstatus").hide();
                    $("#finishstatus").hide();
                }else if(val == 'off'){
                    $("#offstatus").show();
                    $("#onstatus").hide();
                    $("#limitedstatus").hide();
                    $("#autooffstatus").hide();
                    $("#finishstatus").hide();
                }else if(val == 'limited180'){
                    $("#limitedstatus").show();
                    $("#offstatus").hide();
                    $("#onstatus").hide();
                    $("#autooffstatus").hide();
                    $("#finishstatus").hide();
                }else if(val == 'autooff'){
                    $("#autooffstatus").show();
                    $("#offstatus").hide();
                    $("#onstatus").hide();
                    $("#limitedstatus").hide();
                    $("#finishstatus").hide();
                }else if(val == 'finish'){
                    $("#finishstatus").show();
                    $("#offstatus").hide();
                    $("#onstatus").hide();
                    $("#limitedstatus").hide();
                    $("#autooffstatus").hide();
                }
            })
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
