// Lightweight table enhancements: live search + click-to-sort
(function(){
  function init(){
    // Live search: inputs with .js-table-search and data-target selector
    document.querySelectorAll('.js-table-search').forEach(function(input){
      var selector = input.getAttribute('data-target');
      var table = document.querySelector(selector);
      if(!table) return;
      input.addEventListener('input', function(){
        var term = input.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(function(tr){
          var text = tr.textContent.toLowerCase();
          tr.style.display = text.indexOf(term) !== -1 ? '' : 'none';
        });
      });
    });

    // Click-to-sort: tables with data-sortable="true"
    document.querySelectorAll('table[data-sortable="true"]').forEach(function(table){
      var headers = table.querySelectorAll('thead th');
      headers.forEach(function(th, idx){
        th.style.cursor = 'pointer';
        th.addEventListener('click', function(){
          var current = th.getAttribute('data-sort') || 'none';
          var dir = current === 'asc' ? 'desc' : 'asc';
          headers.forEach(function(h){ h.setAttribute('data-sort','none'); });
          th.setAttribute('data-sort', dir);
          sortTable(table, idx, dir);
        });
      });
    });
  }

  function sortTable(table, colIndex, direction){
    var tbody = table.querySelector('tbody');
    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr')).filter(function(r){ return r.style.display !== 'none'; });
    function cellText(row){
      var cell = row.children[colIndex];
      return cell ? (cell.textContent || '').trim() : '';
    }
    var isNumeric = rows.every(function(r){
      var v = cellText(r).replace(/[^0-9.\-]/g, '');
      return v === '' || !isNaN(parseFloat(v));
    });
    rows.sort(function(a,b){
      var va = cellText(a), vb = cellText(b);
      if(isNumeric){
        va = parseFloat(va.replace(/[^0-9.\-]/g, '')) || 0;
        vb = parseFloat(vb.replace(/[^0-9.\-]/g, '')) || 0;
      } else {
        va = va.toLowerCase();
        vb = vb.toLowerCase();
      }
      if(va < vb) return direction === 'asc' ? -1 : 1;
      if(va > vb) return direction === 'asc' ? 1 : -1;
      return 0;
    });
    rows.forEach(function(r){ tbody.appendChild(r); });
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();