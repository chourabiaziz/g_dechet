/*
Template Name: Admin Template
Author: Wrappixel

File: js
*/
// ==============================================================
// Auto select left navbar
// ==============================================================
$(function () {
  "use strict";
  var url = window.location + "";
  var path = url.replace(
    window.location.protocol + "//" + window.location.host + "/",
    ""
  );
  var element = $("ul#sidebarnav a").filter(function () {
    return this.href === url || this.href === path;
  });

  function findMatchingElement() {
    var currentUrl = window.location.href;
    var anchors = document.querySelectorAll("#sidebarnav a");
    for (var i = 0; i < anchors.length; i++) {
      if (anchors[i].href === currentUrl) {
        return anchors[i];
      }
    }
    return null;
  }
  var elements = findMatchingElement();

  if(elements){
    elements.classList.add("active");
  }

  document.querySelectorAll("ul#sidebarnav ul li a.active").forEach(function (link) {
    link.closest("ul").classList.add("in");
    link.closest("ul").parentElement.classList.add("selected");
  });

  document.querySelectorAll("#sidebarnav li").forEach(function (li) {
    const isActive = li.classList.contains("selected");
    if (isActive) {
      const anchor = li.querySelector("a");
      if (anchor) {
        anchor.classList.add("active");
      }
    }
  });

  document.querySelectorAll("#sidebarnav a").forEach(function (link) {
    link.addEventListener("click", function (e) {
      const isActive = this.classList.contains("active");
      const parentUl = this.closest("ul");
      if (!isActive) {
        parentUl.querySelectorAll("ul").forEach(function (submenu) {
          submenu.classList.remove("in");
        });
        parentUl.querySelectorAll("a").forEach(function (navLink) {
          navLink.classList.remove("active");
        });

        const submenu = this.nextElementSibling;
        if (submenu) {
          submenu.classList.add("in");
        }
        this.classList.add("active");
      } else {
        this.classList.remove("active");
        parentUl.classList.remove("active");
        const submenu = this.nextElementSibling;
        if (submenu) {
          submenu.classList.remove("in");
        }
      }
    });
  });

  // Sidebar toggle functionality
  const toggleBtn = document.getElementById("headerCollapse");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const sidebar = document.getElementById("sidebarnav");
      const body = document.body;
      
      if (sidebar) {
        sidebar.classList.toggle("show");
      }
      body.classList.toggle("show-sidebar");
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(event) {
    if (window.innerWidth < 1200) {
      const toggleBtn = document.getElementById("headerCollapse");
      const sidebar = document.getElementById("sidebarnav");
      
      if (sidebar && sidebar.classList.contains('show')) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = toggleBtn && toggleBtn.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickOnToggle) {
          sidebar.classList.remove("show");
          document.body.classList.remove("show-sidebar");
        }
      }
    }
  });
});