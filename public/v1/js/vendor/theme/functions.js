// JavaScript Document

window.onload = function () {
  var wall = new Masonry(document.getElementById('masonry'), {
    columnWidth: 240
  });
};
$(document).ready(function () {
  $(function () {
    $('.header-search input[type=text]').click(function () {
      $(".header-search").addClass("big");
      return false;
    });
    $('.header-search input[type=text]').blur(function () {
      $(".header-search").removeClass("big");
      return false;
    });
    $(function () {
      $('.slides').slidesjs({
        width: 1170,
        play: {
          active: true,
          auto: true,
          interval: 4000,
          swap: true
        }
      });
    });
    $('#masonry div, .thumbnails li, .projectsPlace .place').mouseover(function () {
      $(this).siblings().css({
        opacity: 0.35
      })
    })
      .mouseout(function () {
      $(this).siblings().css({
        opacity: 1
      })
    });
  });
  $('.fancybox').fancybox();
  $('.tooltip-hover').tooltip('hide');
  $('.popover-hover').popover('hide');
  $(function () {
    $(window).scroll(function () {
      if ($(this).scrollTop() != 0) {
        $('#toTop').fadeIn();
      } else {
        $('#toTop').fadeOut();
      }
    });
    $('#toTop').click(function () {
      $('body,html').animate({
        scrollTop: 0
      }, 1000);
    });
  });
  $(function () {
    var stickyHeaderTop = $('#nav-follow').offset().top;
    $(window).scroll(function () {
      if ($(window).scrollTop() > stickyHeaderTop) {
        $('#nav-follow').addClass('nav-follow-fixed');
      } else {
        $('#nav-follow').removeClass('nav-follow-fixed');
      }
    });
  });
  $("#settingsColor ul a").click(function () {
    $this = $(this);
    $this.parent().addClass("active").siblings().removeClass("active");
  });
  $('#settingsColor ul.change-navbar a').click(function () {
    var name = $(this).attr('name');
    if (name == "navbar-static")
      $(".navbar").removeAttr("id");
    else
      $(".navbar").attr("id", "nav-follow");
    return false;
  });
  $('#settingsColor ul.change-color a').click(function () {
    var filename = $(this).parent().data('class') + ".css";
    $('#colorLink').attr('href', 'css/colors/' + filename)
    return false;
  });
  $('#settingsColor ul.change-layout a').click(function () {
    var className = $(this).attr('name');
    if (className === 'wide') {
      $('.change-bg').hide();
      $('body').removeClass('boxed');
      $('body').css('background', 'none').css("background-color", "#f9f9f9");
    } else if (className === 'boxed') {
      $('body').addClass('boxed');
      $('.change-bg').show();
    }
    return false;
  });
  $('#settingsColor ul.change-bg a').click(function () {
    var img = $(this).find('img').attr('src');
    $('body').css('background', 'url(' + img + ')');
    return false;
  });
  $('#filter button').click(function () {
    $(this).css('outline', 'none');
    $('#filter .current').removeClass('current');
    $(this).parent().addClass('current');
    var filterVal = $(this).text().toLowerCase().replace(' ', '-');
    if (filterVal == 'all') {
      $('#portfolio li.hidden').fadeIn('slow').removeClass('hidden');
    } else {
      $('#portfolio li').each(function () {
        if (!$(this).hasClass(filterVal)) {
          $(this).fadeOut('normal').addClass('hidden');
        } else {
          $(this).fadeIn('slow').removeClass('hidden');
        }
      });
    }
    return false;
  });
});