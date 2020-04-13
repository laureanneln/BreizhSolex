
// DataTables
$(document).ready( function () {
  $('.admintable').DataTable({
  });   
} );

// Select 2
$(document).ready(function() {
  $('.select2form select').select2();
});

// Stock

  $('.fa-caret-up').on('click',function(){
    var $qty=$(this).closest('.quantity').find('input');
    var currentVal = parseInt($qty.val());
    $qty.val(currentVal + 1);
  });

  $('.fa-caret-down').on('click',function(){
    var $qty=$(this).closest('.quantity').find('input');
    var currentVal = parseInt($qty.val());
    $qty.val(currentVal - 1);
  });

  ClassicEditor
    .create( document.querySelector( '#editor' ) )
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );
  