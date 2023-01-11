jQuery(function ($) {
  $(".update-post-meta").click(function (e) {
    e.preventDefault();
    
    let $this = $(this);
    var data = {
      action: "push_post_meta",
      post_id: $(this).data("post-id"),
      meta_key: $(this).data("post-meta"),
      meta_value: $(this).data("post-value"),
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(ajaxurl, data, function (response) {
		tb_remove();
      	$('.input-'+$this.data("post-id")+$this.data("post-meta")).html($this.find('svg').prop('outerHTML'));
      	console.log("Got this from the server: " + response);
    });
  });
});
