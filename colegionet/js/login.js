function validar_login(url){
  //atob to decode
  //btoa to encode
  user=document.getElementById('username').value;
  pass=document.getElementById('password').value;
  if (user==''|| password=='') {
    document.getElementById('info').classList.remove('alert-success');
    $('#info').html('<strong>Usuario o contraseña no puede ser nulo</strong>');
    document.getElementById('info').style.display="block";
    document.getElementById('info').classList.add('alert-danger');
    setTimeout ("document.getElementById('info').style.display='none'", 5000);
    setTimeout ("document.getElementById('info').classList.remove('alert-danger')", 5000);

  }else {
    datos={'user':user,'pass':pass};
    datos=JSON.stringify(datos);
    url=atob(url);
    //alert(datos);
    document.getElementById('loading').style.display="flex";
    document.getElementById('loading').classList.remove('fadeOut','animated');
    document.getElementById('loading').classList.add('fadeIn','animated');
    $.post(url,{
        datos:datos
      }).done(function(data) {
        //alert(data);
        document.getElementById('loading').classList.remove('fadeIn','animated');
        document.getElementById('loading').classList.add('fadeOut','animated');
        setTimeout("document.getElementById('loading').style.display='none'",1000);
        switch (data) {
          case '1':
            document.getElementById('info').classList.remove('alert-danger');
            $('#info').html('<strong>Bienvenido</strong>');
            document.getElementById('info').style.display="block";
            document.getElementById('info').classList.add('alert-success');
            setTimeout ("document.getElementById('info').style.display='none'", 5000);
            setTimeout ("document.getElementById('info').classList.remove('alert-success')", 5000);
            window.location='landing';
            break;

          default:
            document.getElementById('info').classList.remove('alert-success');
            $('#info').html('<strong>El usuario o la contraseña son incorrectos</strong>');
            document.getElementById('info').style.display="block";
            document.getElementById('info').classList.add('alert-danger');
            setTimeout ("document.getElementById('info').style.display='none'", 5000);
            setTimeout ("document.getElementById('info').classList.remove('alert-danger')", 5000);

        }
     })
     .fail(function(xhr, status, error){+
       alert(status+" "+error);
       document.getElementById('loading').style.display="none";

       //alert('Error interno del servidor, por favor intente mas tarde');
     });
  }
  event.preventDefault();

}
