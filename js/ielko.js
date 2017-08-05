  jQuery(document).ready(function($) {
    $('#upload-btn').click(function(e) {
      e.preventDefault();
      var image = wp.media({
          title: 'Upload Image',
          multiple: false
        }).open()
        .on('select', function(e) {
          var uploaded_image = image.state().get('selection').first();
          console.log(uploaded_image);
          var image_url = uploaded_image.toJSON().url;
          $('#image_url').val(image_url);
        });
    });

    $('#upload-btn1').click(function(e) {
      e.preventDefault();
      var image1 = wp.media({
          title: 'Upload Image',
          multiple: false
        }).open()
        .on('select', function(e) {
          var uploaded_image = image1.state().get('selection').first();
          console.log(uploaded_image);
          var image_url = uploaded_image.toJSON().url;
          $('#image_url1').val(image_url);
        });
    });

    $('#roku_app').on('click', function(e) {
      $.post("http://factory.upg.gr/index.php", {
          name: this.attr("var1"),
          city: "Duckburg"
        },
        function(data, status) {
          console.log("Data: " + data);
        });

    });

    $('#upload-btn2').click(function(e) {
      e.preventDefault();
      var image2 = wp.media({
          title: 'Upload Image',
          multiple: false
        }).open()
        .on('select', function(e) {
          var uploaded_image = image2.state().get('selection').first();
          console.log(uploaded_image);
          var image_url = uploaded_image.toJSON().url;
          $('#image_url2').val(image_url);
        });
    });
    $('#upload-btn3').click(function(e) {
      e.preventDefault();
      var image3 = wp.media({
          title: 'Upload Image',
          multiple: false
        }).open()
        .on('select', function(e) {
          var uploaded_image = image3.state().get('selection').first();
          console.log(uploaded_image);
          var image_url = uploaded_image.toJSON().url;
          $('#image_url3').val(image_url);
        });
    });

  });