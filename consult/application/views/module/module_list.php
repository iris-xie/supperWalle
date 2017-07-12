<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sys_name; ?></title>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
    <link href="<?php echo $static_prefix; ?>css/bootstrap.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/font-awesome.min.css?v=<?php echo $static_version; ?>"
          rel="stylesheet">
    <link
        href="<?php echo $static_prefix; ?>css/plugins/dataTables/dataTables.bootstrap.css?v=<?php echo $static_version; ?>"
        rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/iCheck/custom.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/chosen/chosen.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/colorpicker/css/bootstrap-colorpicker.min.css"
          rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/cropper/cropper.min.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/switchery/switchery.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/datapicker/datepicker3.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
          rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/clockpicker/clockpicker.css" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>模块添加</h5>
                </div>
                <div class="ibox-content">

                    <div class="">
                        <?php if ($add_pri): ?>
                            <a onclick="add_module()" href="javascript:void(0);" class="btn btn-primary add_module">添加模块</a>
                        <?php endif; ?>
                    </div>
                    <table class="table table-striped table-bordered table-hover " id="editable">

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3 class="m-t-none m-b modal-title">添加模块</h3>
                <form class="form-horizontal addUserForm" method="get" action="<?php echo  site_url('module/add_module')?>">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">描述：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="描述" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">模块：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="模块" class="form-control" name="module">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">动作：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="动作" class="form-control" name="action">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">备注：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="备注" class="form-control" name="remark">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button class="btn btn-sm btn-primary" type="button" id="module_add" onclick="module_add_func()">保 存</button>
                            <button class="btn btn-sm btn-white" type="reset" data-dismiss="modal">取 消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="module_edit_modal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3 class="m-t-none m-b modal-title">修改</h3>
                <form class="form-horizontal addUserForm" method="get" action="<?php echo  site_url('module/edit_module')?>">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">名称：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="" class="form-control" name="name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">模块：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="模块" class="form-control" name="module" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">动作：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="动作" class="form-control" name="action" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">备注：</label>
                        <div class="col-sm-8">
                            <input type="input" placeholder="备注" class="form-control" name="remark" value="">
                        </div>
                    </div>
                    <input type="hidden" placeholder="动作" class="form-control" name="id" value="">
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button class="btn btn-sm btn-primary" type="button" id="module_edit" onclick="edit_module()">保 存</button>
                            <button class="btn btn-sm btn-white" type="reset" data-dismiss="modal">取 消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</body>
<script src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
<script src="<?php echo $static_prefix; ?>js/bootstrap.min.js"></script>


<script src="<?php echo $static_prefix; ?>js/plugins/jeditable/jquery.jeditable.js"></script>

<!-- Data Tables -->
<script src="<?php echo $static_prefix; ?>js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="<?php echo $static_prefix; ?>js/plugins/dataTables/dataTables.bootstrap.js"></script>

<!-- Page-Level Scripts -->
<script>
    function add_module()
    {
        $("#modal-form").modal();
    }
    function edit_row(obj)
    {
        $("#module_edit_modal").modal();

        $("#module_edit_modal").find("input[name='name']").val($(obj).parent().siblings().eq(1).text());
        $("#module_edit_modal").find("input[name='module']").val($(obj).parent().siblings().eq(2).text());
        $("#module_edit_modal").find("input[name='action']").val($(obj).parent().siblings().eq(3).text());
        $("#module_edit_modal").find("input[name='remark']").val($(obj).parent().siblings().eq(4).text());
        $("#module_edit_modal").find("input[name='id']").val($(obj).parent().siblings().eq(0).text());

    }

    function del_row(obj)
    {
		parent.layer.confirm("确定要删除吗？", {
			icon: 3,
			btn: ['确定','取消']
		}, function() {
			$.ajax({
			type: 'GET',
			url: '<?php echo site_url('module/del_module');?>',
			dataType: 'json',
			data: {
				id:$(obj).parent().siblings().eq(0).text(),
				module:$(obj).parent().siblings().eq(2).text(),
				name:$(obj).parent().siblings().eq(3).text()
			},
			success: function(res) {
				parent.layer.close();
				if (res.status == 1) {
					parent.layer.msg(res.msg, {icon: 1, time:2000}, function(){
						window.location.reload();
					});
				} else {
					parent.layer.msg(res.msg, {icon: 2});
				}
			},
			error: function(aXhr) {
			},
			complete: function() {
			}
		});
		}, function(){
			parent.layer.close();
		});
    }
    function edit_module()
    {
		$.ajax({
			type: 'GET',
			url: '<?php echo site_url('module/edit_module');?>',
			dataType: 'json',
			data: {
				id:$("#module_edit_modal").find("input[name='id']").val(),
				module:$("#module_edit_modal").find("input[name='module']").val(),
				name:$("#module_edit_modal").find("input[name='name']").val(),
				remark:$("#module_edit_modal").find("input[name='remark']").val(),
				action:$("#module_edit_modal").find("input[name='action']").val()
			},
			success: function(res) {
				if (res.status == 1) {
					parent.layer.msg(res.msg);
					setInterval("location.reload()",2000);
				} else {
					parent.layer.msg(res.msg);
				}
			},
			error: function(aXhr) {
				console.log(aXhr);
			},
			complete: function() {
			}
		});
    }

    function module_add_func()
    {
            $.ajax({
                type: 'GET',
                url: '<?php echo site_url('module/add_module');?>',
                dataType: 'json',
                data: {
                    module:$("#modal-form").find("input[name='module']").val(),
                    name:$("#modal-form").find("input[name='name']").val(),
                    remark:$("#modal-form").find("input[name='remark']").val(),
                    action:$("#modal-form").find("input[name='action']").val()
                },
                success: function(res) {
                    if (res.status == 1) {
                        parent.layer.msg(res.msg);
                        $('#modal-form').modal('hide');
                        setInterval("location.reload()",2000);
                    } else {
                        parent.layer.msg(res.msg);
                        $('#modal-form').modal('hide');
                        setInterval("location.reload()",2000);
                    }
                },
                error: function(aXhr) {
                    console.log(aXhr);
                },
                complete: function() {
                }
            });
    }

    $(document).ready(function () {
       var a =$("#editable").dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "<?php echo site_url('module/module_list')?>",
            "columns": [
                { "data": "id", "title":"序号","defaultContent":"",className:"id","searchable":false},
                { "data": "name", "title":"描述","defaultContent":"",className:"name","searchable":true},
                { "data": "module", "title":"模块","defaultContent":"",className:"module","searchable":true},
                { "data": "action", "title":"动作","defaultContent":"",className:"action","searchable":true},
                { "data": "remark", "title":"备注","defaultContent":"",className:"remark","searchable":false},
                { "data": null, "title":"操作","orderable":false,"searchable":false,"defaultContent": "<?php if ($edit_pri): ?><button class='btn btn-outline btn-primary edit-row' type='button' onclick=\"edit_row(this)\">编辑</button><?php endif; ?>&nbsp;&nbsp;<?php if ($del_pri): ?><button class='btn btn-outline btn-danger edit' type='button' onclick=\"del_row(this)\">删除</button><?php endif; ?>"}
            ]
       });
        $(".add_module").click(function(){

            $("#modal-form").modal();
        });
    });
    /*$( document ).ajaxComplete(function( event, xhr, settings ) {*/
        /*$(".name,.module,.action,.remark").editable("<?php //echo site_url('module/edit_module')?>",
            {
            "tooltip": 'Click to edit...',
            "editBy":'dblclick',
            "submitBy": 'blur',
            "callback": function (d, c) {
                var b = a.fnGetPosition(this);
                a.fnUpdate(d, b[0], b[1])
            },
            "submitdata": function (c, b) {
                return {"row_id": this.parentNode.getAttribute("id"), "column": a.fnGetPosition(this)[2]}
            },
            "width": "90%",
            "height": "100%"
        });*/


    /*});*/

</script>
<!--<script>
    $(document).ready(function () {
        $(".dataTables-example").dataTable();
        var a = $("#editable").dataTable();
        a.$("td").editable("../example_ajax.php", {
            "callback": function (d, c) {
                var b = a.fnGetPosition(this);
                a.fnUpdate(d, b[0], b[1])
            }, "submitdata": function (c, b) {
                return {"row_id": this.parentNode.getAttribute("id"), "column": a.fnGetPosition(this)[2]}
            }, "width": "90%", "height": "100%"
        })
    });

</script>-->
