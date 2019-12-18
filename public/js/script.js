  $('.save').hide();

  // Plus moins panier
    $('.fa-plus-circle').on('click',function(){
        $(this).closest('.quantity').find('.save').show();
        var $qty=$(this).closest('.quantity').find('input[type="text"]');
        var currentVal = parseInt($qty.val());
        $qty.val(currentVal + 1);
      });
    
      $('.fa-minus-circle').on('click',function(){
        $(this).closest('.quantity').find('.save').show();
        var $qty=$(this).closest('.quantity').find('input[type="text"]');
        var currentVal = parseInt($qty.val());
        $qty.val(currentVal - 1);
      });

    // Hide update address
    $('.address__update').hide();

    $('.address__choice a').on('click', function() {
        $('.address__update').show();
    });

    $('.address__update .far').on('click', function() {
        $('.address__update').hide();
    });
      
// Categories menu
// On cache les sous-menus :
$(".catnav ul ul").hide();  

$(".catnav .catnav-category > a").click( function () {
    // Si le sous-menu était déjà ouvert, on le referme :
    if ($(this).next("ul:visible").length != 0) {
        $(this).next("ul").slideUp("normal");
        $(this).find("i").addClass("fa-plus");
        $(this).find("i").removeClass("fa-minus");
    }
    // Si le sous-menu est caché, on ferme les autres et on l'affiche :
    else {
        $(this).next("ul").slideUp("normal", function() {
            $(this).find("i").addClass("fa-minus");
            $(this).find("i").removeClass("fa-plus");
        });
        $(this).next("ul").slideDown("normal");
        $(this).find("i").addClass("fa-minus");
        $(this).find("i").removeClass("fa-plus");
    }
    // On empêche le navigateur de suivre le lien :
    return false;
});    

// Owl carousel
$('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    autoplay:true,
    autoplayTimeout: 3000,
    responsive:{
        0:{
            items:1
        }
    }
});

$('*').waitForImages(function() {
    setTimeout(function() {
        new WOW().init();
    }, 3000);
    setTimeout(function() {
        $('.loader').css('display', 'none');
    }, 3000);
});

// About tabs
function openCity(evt, cityName) {

    // Declare all variables
    var i, tabcontent, tablinks;
  
    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("about__tab");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
  
    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("about__buttons_button");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
  
    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "flex";
    evt.currentTarget.className += " active";
  }
  
  // Get the element with id="defaultOpen" and click on it
  document.getElementById("defaultOpen").click();

