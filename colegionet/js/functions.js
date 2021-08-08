$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    if ($('#info').length>0) {
      setTimeout("salidainfo()",10000); 
    }
});
var timer;
var timer2;
function salidainfo() {
  $('#info').fadeOut('slow');
  setTimeout("$('#info').remove()",1000);
}

var dialog;
function showPleaseWait(message,type='info'){
  divs =  '<div class="pleasewait" id="pleasewait">\
              <div style="margin: auto;" id="pleasewait2">\
                  <span class="fa fa-spin fa-circle-o-notch"></span>\
                  <h1 id="modal-message" class="text-center messagetitle">'+message+'</h1>\
              </div>\
          </div>';
  $(document.body).append(divs);
}

function hidePleaseWait(){
  //$('#bar2').css('width','0%');
  //$('#bar').css('width','100%');
  //setTimeout("dialog.modal('hide')",250);
  $('#pleasewait').remove();
}

/*function showPleaseWait(message,type='info'){
  message = "<p id='modal-message' class='animated'>"+message+"</p>"
  dialog = bootbox.dialog({
    title: message,
    message: '<p><i class="fa fa-spin fa-circle-o-notch"></i></p>',
    //message: '<div class="progress">\
    //            <div id="bar" class="p0 progress-bar progress-bar-striped active" role="progressbar"\
    //              aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:0%">\
    //            </div>\
    //            <div id="bar2" class="p0 progress-bar progress-bar-'+type+' progress-bar-striped active" role="progressbar"\
    //              aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">\
    //            </div>\
    //          </div>',
    closeButton: false
  });
}*/
/*
function hidePleaseWait(){
  //$('#bar2').css('width','0%');
  //$('#bar').css('width','100%');
  //setTimeout("dialog.modal('hide')",250);
  dialog.modal('hide');
}
function changeProgress(percent){
  percent2 = 100-percent;
  $('#bar2').css('width',percent2+'%');
  $('#bar').css('width',percent+'%');
}
*/
function changeMessagePleaseWait(message){
  message = "<h1 id='modal-message' class='animated fadeInDown text-center messagetitle'>"+message+"</h1>"
  $('#modal-message').css('position','absolute');
  $('#modal-message').addClass('animated fadeOutDown');
  setTimeout("$('#modal-message').remove()",1000);
  clearTimeout(timer2);
  timer2 = setTimeout(changingTheModalMessage(message),1000);
}
function changingTheModalMessage(message){
  $('#pleasewait').find('#pleasewait2').append(message);
}
/*
function changingTheModalMessage(message){
  dialog.find('.modal-header').append(message);
}
*/
function messageinfo(message,success){
  messageClass='alert ';
  if (success) {
    title='Completado';
    messageClass+='alert-success';
  }else{
    title='Ha ocurrido un error';
    messageClass+='alert-danger';
  }
  if (!$('#info').length>0) {
    messageInfo='\
    <div id="info" class="col-xs-11 '+messageClass+'">\
      <h4>'+title+'</h4>\
      <p>'+message+'</p>\
    </div>';
    $(document.body).append(messageInfo);
    $('#info').removeAttr('style');
    clearTimeout(timer);
    timer=setTimeout("salidainfo()",7500);
  }else{
    messageInfo='\
      <h4>'+title+'</h4>\
      <p>'+message+'</p>';
    if (success) {
      $('#info').removeClass('alert-danger');
      $('#info').addClass('alert-success');
    }else{
      $('#info').removeClass('alert-success');
      $('#info').addClass('alert-danger');
    }
    $('#info').html(messageInfo);
    $('#info').removeAttr('style');
    clearTimeout(timer);
    timer=setTimeout("salidainfo()",7500);
  }
}

function limpiarCampos(form){
  $(form).find('p').each(function(){
    $(this).html('');
  })
  $(form).find('input,textarea,select').each(function(){
    $(this).removeAttr('style');
  })
}

function validarFormulario(func,form){
  return func(form);
}

function obtenerUrlAcceso(url){
  urlnew = url.split('/');
  if (!urlnew[5]) {urlnew[5]='index'}
  urlAcceso = urlnew[3]+"::"+urlnew[4]+"::"+urlnew[5];
  return urlAcceso;
}