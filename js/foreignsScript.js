$(document).ready(function(){
    var href = "/foreigns/crudForeigner.php";//Адрес файла на сервере относительный
	
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
	
	
	$('#searchForm').live('submit',function (event) {// форма поиска
        var value = $('#searchForm [name=search]').val();
		
        if(value !== '' && (value.search(/^\s/) === - 1)) {
            var url = $('#searchForm').attr('action') + "/search/" + value + '/';//генерируем в соответствие с htaccess
            document.location = url;
            //$("#foreigners").replaceText( /(value)/gi, "value" );
        }
		
        return false;

	});


	$(document).keydown(function(e) {//Закрытие окна - клавиша "Esc"
	    if( e.keyCode === 27 ) {
		    $('.dialog_box_wraper').hide();
			$('.dialog_box_cont').hide();
			$('.dialog_box_ok').hide();
		}
	});
	
    $('.dialog_box_exit').click(function(){//Закрытие окна кнопка "отмена"
        $('.dialog_box_wraper').hide();
        $('.dialog_box_cont').hide();
        $('.dialog_box_ok').hide();
	});
	
	$('.fadd').live('click',function (event) {//Добавление иностранного студента
    $("head").append('<link REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"/jquery-ui.css\">');
    $('.dialog_box').width(600).height(230);
    $('.dialogHeader h2').text('Добавление');
    $('.dialog_box_inner').empty();
    $('.dialog_box_ok').unbind('click');
	$('.dialog_box_ok').text('Добавить');

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
            else $('.dialog_box_inner').empty().append('<div class="error">Произошла непредвиденная ошибка при добавлении студента. Попробуйте ещё раз.');
        }
    });
	
    });
    return false;
	});
	
	$('.del').live('click',function (event) {//подтверждение удаления в архив
        $('.dialog_box').width(380).height(160);
        $('.dialogHeader h2').text('Подтверждение архивации');
        $('.dialog_box_inner').text('Вы действительно хотите перенести запись в архив?');
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('Да');

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

    $('.delFromArchiv').live('click',function (event) {//подтверждение удаления из БД
        $('.dialog_box').width(380).height(160);
        $('.dialogHeader h2').text('Подтверждение удаления записи из БД');
        $('.dialog_box_inner').text('Вы действительно хотите удалить запись из архива? Запись будет удалена навсегда!!!');
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('Да');

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
    $('.importcsv').live('click', {action: 'importcsv', title: "Импорт из csv файла"}, importCsv);
    $('.fio').live('click', {action: 'updatefio', title: 'Изменение ФИО'}, editData);
    $('.country').live('click', {action: 'updatecountry', title: 'Изменение страны'}, editData);
    $('.invitDate').live('click', {action: 'updateinvitationDate', title: 'Дата согласования приглашения в ОГиМ'}, editData);
    $('.invitation').live('click', {action: 'updateinvitation', label: 'Кто ходатайствует о приглашении', title: 'Оформление приглашения на учебу'}, editData);
    $('.formNumber').live('click', {action: 'updateFormNumber', label: '№ бланка приглашения', title: 'Оформление приглашения на учебу'}, editData);
    $('.residence').live('click', {action: 'updateres',  label: '№ ходатайства', title: 'Оформление временного пребывания'}, editData);
    $('.educationcontract').live('click', {action: 'updateedu', label: '№', title: 'Договор на обучение'}, editData);
    $('.hostelorder').live('click', {action: 'updatehor', label: '№', title: 'Оформление ордера на проживание в общежитии'}, editData);
    $('.hostelcontract').live('click', {action: 'updatehoc', label: '№', title: 'Договор на проживание'}, editData);
    $('.zaselenie').live('click', {action: 'updatezas', label: '№', title: 'Приказ на заселение'}, editData);
    $('.ispolkom').live('click', {action: 'updateisp', label: '№ исходящего письма', title: 'Регистрация'}, editData);
    $('.medicalcheckup').live('click', {action: 'updatemed', title: 'Прохождение медосмотра'}, editData);
    $('.enrollment').live('click', {action: 'updateenr', label: '№', title: 'Приказ о зачислении'}, editData);
    $('.petition').live('click', {action: 'updatepet', label: '№ исходящего письма', title: 'Ходатайство на временное проживание'}, editData);
    $('.status').live('click', {action: 'updatestatus', title: 'Изменение статуса'}, editData);
    $('.whoInvites').live('click', {action: 'updateWhoInvites', label: 'Название структурного подразделения, выдавшее приглашение', title: 'Структурное подразделение, выдавшее приглашение'}, editData);
    $('.actionEndDate').live('click', {action: 'updateActionEndDate', title: 'Дата окончания действия приглашения'}, editData);
    $('.note').live('click', {action: 'updateNote', label: 'Введите текст заметки', title: 'Примечание'}, editData);
	
	
    function editData (eventObject) {//функция редактировани этапов
        $("head").append('<link REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"/jquery-ui.css\">');
        $('.dialog_box').width(600).height(280);
        $('.dialogHeader h2').text(eventObject.data.title);
        $('.dialog_box_inner').empty();
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('Сохранить');

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
                else editElement.text("добавить");
                $('.dialog_box_wraper').hide();
                $('.dialog_box_cont').hide();
                $('.dialog_box_ok').hide();
            }
        });
	
    });
	
	   return false;
    }
	
    function printOrder(eventObject)// печать ордера
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
			
        var content1 = '<html><head><title>Печать ордера</title>' + style +
            '</head><body><div class="university">БЕЛОРУССКИЙ ГОСУДАРСТВЕННЫЙ МЕДИЦИНСКИЙ УНИВЕРСИТЕТ</div><div class="header1">' +
            dekanat;
        var content2 = norder + '</div><div class="header2">на жилое помещение в общежитии</div><div class="student">Студенту ' + fio +
            '</div><div class="footer"><p>предоставлено место в комнате №____ обжежития № ____</p>' +
            '<p>Ордер выдан на основании приказа ректора №' + prikaz + ' от ' + prikazdate + ' г.</p><p>Декан факультета ' +
            '________________________ ' + dekan + '</p><p>Директор студгородка _____________________ А.Ю. Катичев</p>' +
			'</div><p class="date">' + dateorder + '</p></body></html>';
        var win = window.open("about:blank", "print", "width=700,height=800");
		win.document.write(content1 + ' ОРДЕР №' + content2 + '<br><br>' + content1 + ' КОРЕШОК ОРДЕРА №' + content2);
        win.print();
        win.close();
    }
	
    function importCsv (eventObject) {//импорт
        $('.dialog_box').width(600).height(280);
        $('.dialogHeader h2').text(eventObject.data.title);
        $('.dialog_box_inner').empty();
        $('.dialog_box_ok').unbind('click');
        $('.dialog_box_ok').text('Выполнить');

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
