<script>
$(document).ready(function(){
  $('.department-carousel').owlCarousel({
    loop: true,
    margin: 20,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    responsive: {
      0:   { items: 1 },
      576: { items: 2 },
      768: { items: 3 },
      992: { items: 4 }
    }
  })
});
</script>
