$('textarea').ckeditor();

$('#cancel').click(function(e) {
  e.preventDefault();
  window.location.href = '<?php echo $this->baseUrl(); ?>' + '/site';
});
