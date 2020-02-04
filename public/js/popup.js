
  // popup
  $('.popup').hide();

  $('.cancel').on('click', function() {
      $('.popup').show();
  });

  $('.popup button').on('click', function() {
      $('.popup').hide();
  })