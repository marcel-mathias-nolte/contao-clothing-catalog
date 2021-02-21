$(function() {
   $('.ce_clothing_catalog_filter nav.properties li input').change(function() {
      window.location.href = $(this).data('href');
   });
   $('.ce_clothing_catalog_filter nav.options li select').change(function() {
      window.location.href = $(this).find('option:selected').data('href');
   });
});