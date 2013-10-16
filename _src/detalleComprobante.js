function mostrarPeticion(nIdPeticion){
   
   $("#mostarPeticion" + nIdPeticion).dialog({
            resizable: false,
            autoOpen:false,
            modal:true,
            width:800,
            draggable: false
   });
   
   $("#mostarPeticion" + nIdPeticion).dialog({});
                
   $('#mostarPeticion' + nIdPeticion).dialog('open');
   
   return false;
}

function mostrarRespuesta(nIdRespuesta){
   $("#mostarRespuesta" + nIdRespuesta).dialog({
            resizable: false,
            autoOpen:false,
            modal:true,
            width:800,
            draggable: false
   });
      
   $("#mostarRespuesta" + nIdRespuesta).dialog({});
                
   $('#mostarRespuesta' + nIdRespuesta).dialog('open');
   
   return false;
}