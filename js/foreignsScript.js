$(document).ready(function(){
    var href = "/foreigns/crudForeigner.php";//����� ����� �� ������� �������������
	
    $('.manual-img').click(function(event) {
        var win = window.open("about:blank", "image", "width=1920,height=1000");
        win.document.write('<img src="' + $(this).attr('src') +'"width="1920" alt="' + $(this).attr('alt') + '">');
        win.onblur = function() {
            win.close();
        }
    });
	
    $('.csv').click(function (){
        $('.csvInto').not($(this).children('.csvInto')).fadeOut();
        $(this).toggleClass('mobileMenuActiv');
        $(this).children('.csvInto').fadeToggle();
    });

    $(function(){
        $(document).click(function(event) {
            if ($(event.target).hasClass("csv")) return;
		    $('.csv').removeClass('mobileMenuActiv');
            $('.csvInto').fadeOut();
            event.stopPropagation();
        });
    });
	
	
	$('#searchForm').live('submit',function (event) {// ����� ������
        var value = $('#searchForm [name=search]').val();
		
        if(value !== '' && (value.search(/^\s/) === - 1)) {
            var url = $('#searchForm').attr('action') + "/search/" + value + '/';//���������� � ������������ � htaccess
            document.location = url;
            //$("#foreigners").replaceText( /(value)/gi, "value" );
        }
		
        return false;

	});


	$(document).keydown(function(e) {//�������� ���� - ������� "Esc"
	    if( e.keyCode === 27 ) {
		    $('.dialog_box_wraper').hide();
			$('.dialog_box_cont').hide();
			$('.dialog_box_ok').hide();
		}
	});
	
    $('.dialog_box_exit').click(function(){//�������� ���� ������ "������"
        $('.dialog_box_wraper').hide();
        $('.dialog_box_cont').hide();
        $('.dialog_box_ok').hide();
	});
	
	$('.fadd').live('click',function (event) {//���������� ������������ ��������
    $("head").append('<link REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"/jquery-ui.css\">');
    $('.dialog_box').width(600).height(230);
    $('.dialogHeader h2').text('����������');
    $('.dialog_box_inner').empty();
    $('.dialog_box_ok').unbind('click');
	$('.dialog_box_ok').text('��������');

	$('.dialog_box_wraper').show();
    $('.dialog_box_cont').show();
	$('.dialog_box_ok').show();
	
    $.ajax({
		type: "POST",
        url: href,
		data: 'action=beforeadd',
        success: function(data){
            $('.dialog_box_inner').append(data);
            $('.dialog_box_inner').find(".datepicker").datepicker($.datepicker.regional[ "ru" ]);
        }
    });
	
	$('.dialog_box_ok').click(function(){
    var formData = $('#addForm').serialize() + '&action=add';
    var fio = $('[name=fio]').val();

	$.ajax({
		type: "POST",
        url: href,
		data: formData,
        success: function(data){
            if(data){
                if(!isNaN(data)){
                    fdata = 'action=addForeigner&id=' + data + '&fio=' + fio;
	                $.ajax({
				        type: "POST",
                        url: href,
                        data: fdata,
                        success: function(result){
					        if(result){
                                $('#foreigners').prepend(result);
                            }
                        }
                    });
					
                    
                    $('.dialog_box_wraper').hide();
                    $('.dialog_box_cont').hide();
                    $('.dialog_box_ok').hide();
                }
                else {
                    $('.dialog_box_inner').empty().append(data);
                    $('.dialog_box_inner').find(".datepicker").datepicker($.datepicker.regional[ "ru" ]);
                }
            }
            else $('.dialog_box_inner').empty().append('<div class="error">��������� �������������� ������ ��� ���������� ��������. ���������� ��� ���.');
        }
    });
	
    });
    return false;
	});
	
	$('.del').live('click',function (event) {//������������� �������� � �����
        $('.dialog_box').width(380).height(160);
        $('.dialogHeader h2').text('������������� ���������');
        $('.dialog_box_inner').text('�� ������������� ������ ��������� ������ � �����?');
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('��');

        var delElement = $(this).parents('tr');
        var delData = 'action=del&id=' + $(this).parents('tr').attr('id');

		$('.dialog_box_wraper').show();
        $('.dialog_box_cont').show();
        $('.dialog_box_ok').show();
	    
		$('.dialog_box_ok').click(function(){
			
	        $.ajax({
				type: "POST",
                url: href,
                data: delData,
                success: function(data){
					if(data){
                        delElement.remove();
                        $('.dialog_box_wraper').hide();
                        $('.dialog_box_cont').hide();
                        $('.dialog_box_ok').hide();
                    }
                }
            });
			
	    });
	   
	   return false;
    });

    $('.delFromArchiv').live('click',function (event) {//������������� �������� �� ��
        $('.dialog_box').width(380).height(160);
        $('.dialogHeader h2').text('������������� �������� ������ �� ��');
        $('.dialog_box_inner').text('�� ������������� ������ ������� ������ �� ������? ������ ����� ������� ��������!!!');
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('��');

        var delElement = $(this).parents('tr');
        var delData = 'action=delFromArchiv&id=' + $(this).parents('tr').attr('id');

        $('.dialog_box_wraper').show();
        $('.dialog_box_cont').show();
        $('.dialog_box_ok').show();

        $('.dialog_box_ok').click(function(){

            $.ajax({
                type: "POST",
                url: href,
                data: delData,
                success: function(data){
                    if(data){
                        delElement.remove();
                        $('.dialog_box_wraper').hide();
                        $('.dialog_box_cont').hide();
                        $('.dialog_box_ok').hide();
                    }
                }
            });

        });

        return false;
    });
	
    $('.print').live('click', {action: 'print_order'}, printOrder);
    $('.importcsv').live('click', {action: 'importcsv', title: "������ �� csv �����"}, importCsv);
    $('.fio').live('click', {action: 'updatefio', title: '��������� ���'}, editData);
    $('.country').live('click', {action: 'updatecountry', title: '��������� ������'}, editData);
    $('.invitDate').live('click', {action: 'updateinvitationDate', title: '���� ������������ ����������� � ����'}, editData);
    $('.invitation').live('click', {action: 'updateinvitation', label: '��� ������������� � �����������', title: '���������� ����������� �� �����'}, editData);
    $('.formNumber').live('click', {action: 'updateFormNumber', label: '� ������ �����������', title: '���������� ����������� �� �����'}, editData);
    $('.residence').live('click', {action: 'updateres',  label: '� �����������', title: '���������� ���������� ����������'}, editData);
    $('.educationcontract').live('click', {action: 'updateedu', label: '�', title: '������� �� ��������'}, editData);
    $('.hostelorder').live('click', {action: 'updatehor', label: '�', title: '���������� ������ �� ���������� � ���������'}, editData);
    $('.hostelcontract').live('click', {action: 'updatehoc', label: '�', title: '������� �� ����������'}, editData);
    $('.zaselenie').live('click', {action: 'updatezas', label: '�', title: '������ �� ���������'}, editData);
    $('.ispolkom').live('click', {action: 'updateisp', label: '� ���������� ������', title: '�����������'}, editData);
    $('.medicalcheckup').live('click', {action: 'updatemed', title: '����������� ����������'}, editData);
    $('.enrollment').live('click', {action: 'updateenr', label: '�', title: '������ � ����������'}, editData);
    $('.petition').live('click', {action: 'updatepet', label: '� ���������� ������', title: '����������� �� ��������� ����������'}, editData);
    $('.status').live('click', {action: 'updatestatus', title: '��������� �������'}, editData);
    $('.whoInvites').live('click', {action: 'updateWhoInvites', label: '�������� ������������ �������������, �������� �����������', title: '����������� �������������, �������� �����������'}, editData);
    $('.actionEndDate').live('click', {action: 'updateActionEndDate', title: '���� ��������� �������� �����������'}, editData);
    $('.note').live('click', {action: 'updateNote', label: '������� ����� �������', title: '����������'}, editData);
	
	
    function editData (eventObject) {//������� ������������� ������
        $("head").append('<link REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"/jquery-ui.css\">');
        $('.dialog_box').width(600).height(280);
        $('.dialogHeader h2').text(eventObject.data.title);
        $('.dialog_box_inner').empty();
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('���������');

        var editElement = $(this);
        var id = $(this).parents('tr').attr('id');
        var editData = 'action=before_' + eventObject.data.action + '&id=' + id;

		$('.dialog_box_wraper').show();
        $('.dialog_box_cont').show();
        $('.dialog_box_ok').show();
		
        $.ajax({
		    type: "POST",
            url: href,
		    data: editData,
            success: function(data){
                $('.dialog_box_inner').append(data);
                $('.dialog_box_inner').find('#describe').text(eventObject.data.label);
                $('.dialog_box_inner').find(".datepicker").datepicker($.datepicker.regional[ "ru" ]);
            }
        });
	
	$('.dialog_box_ok').click(function(){
        editData = $('#addForm').serialize() + '&action=' + eventObject.data.action + '&id=' + id;

	    $.ajax({
		    type: "POST",
            url: href,
		    data: editData,
            success: function(data){
                if(data){
                    editElement.html(data);
                }
                else editElement.text("��������");
                $('.dialog_box_wraper').hide();
                $('.dialog_box_cont').hide();
                $('.dialog_box_ok').hide();
            }
        });
	
    });
	
	   return false;
    }
	
    function printOrder(eventObject)// ������ ������
    {
        var elem = $(this).parents('tr');
        var fio = '<span class="fio">' + elem.find('.fiotd .text').text() + '</span>';
        var prikaz = elem.find('.zaselenietd .text').text();
        var prikazdate = elem.find('.zaselenietd .date').text();
        var dekanat = elem.find('.fiotd .department').text();
        var dekan = $('#dekan').text();
        var norder = elem.find('.hostelordertd .text').text();
        var dateorder = elem.find('.hostelordertd .date').text();
       
		
        var style = "<style>.university{text-align:center;font-size:11px;margin-bottom:15px;}.header1, .header2 {text-align:center;font-weight:bold;}" +
            ".student{margin:10px 0 10px 0;}.student .fio{font-weight:bold;}</style>";
			
        var content1 = '<html><head><title>������ ������</title>' + style +
            '</head><body><div class="university">����������� ��������������� ����������� �����������</div><div class="header1">' +
            dekanat;
        var content2 = norder + '</div><div class="header2">�� ����� ��������� � ���������</div><div class="student">�������� ' + fio +
            '</div><div class="footer"><p>������������� ����� � ������� �____ ��������� � ____</p>' +
            '<p>����� ����� �� ��������� ������� ������� �' + prikaz + ' �� ' + prikazdate + ' �.</p><p>����� ���������� ' +
            '________________________ ' + dekan + '</p><p>�������� ����������� _____________________ �.�. �������</p>' +
			'</div><p class="date">' + dateorder + '</p></body></html>';
        var win = window.open("about:blank", "print", "width=700,height=800");
		win.document.write(content1 + ' ����� �' + content2 + '<br><br>' + content1 + ' ������� ������ �' + content2);
        win.print();
        win.close();
    }
	
    function importCsv (eventObject) {//������
        $('.dialog_box').width(600).height(280);
        $('.dialogHeader h2').text(eventObject.data.title);
        $('.dialog_box_inner').empty();
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('���������');

        var editData = 'action=before_' + eventObject.data.action;

		$('.dialog_box_wraper').show();
        $('.dialog_box_cont').show();
        $('.dialog_box_ok').show();
	    
        $.ajax({
		    type: "POST",
            url: href,
		    data: editData,
            success: function(data){
                $('.dialog_box_inner').append(data);
            }
        });
	
	$('.dialog_box_ok').click(function(){
        var file = document.forms.import1.filename.files[0];
        if(file !== undefined) {
            var formData = new FormData();
            formData.append('filename', document.forms.import1.filename.files[0]);

	        $.ajax({
		        type: "POST",
                url: href,
		        data: formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                success: function(data){
                    if(data) $('.dialog_box_inner').empty().append(data);
                    else document.location.reload();
                }
            });
		
        }
	
    });
	
	   return false;
    }
});
