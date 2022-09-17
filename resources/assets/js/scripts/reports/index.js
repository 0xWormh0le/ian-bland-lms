$(document).ready(function () {

  $(".select2").select2({ width: '100%' });

  $(".multiselect").multiselect({
    buttonWidth: '100%',
    includeSelectAllOption: true
  });

  $("#filter_courses").multiselect({
    nonSelectedText: $("#filter_courses").data("label"),
    buttonWidth: '100%',
    onChange: function (element, checked) {
      getModuleByCourse();
    }
  });
  getModuleByCourse();

  $("#filter_teams").multiselect({
    nonSelectedText: $("#filter_teams").data("label"),
    buttonWidth: '100%',
    includeSelectAllOption: true,
    onChange: function (element, checked) {
      getUserByTeam();
    },
    onSelectAll: function () {
      getUserByTeam();
    },
    onDeselectAll: function () {
      getUserByTeam();
    },
  });

  function getUserByTeam(){
    var opt = $('#filter_teams option:selected');
    var team_id = [];
    $(opt).each(function (index, brand) {
      team_id.push([$(this).val()]);
    });
    if (team_id.length > 0)
    {
      $.ajax({
        type: 'GET',
        url: $("#filter_teams").data('url'),
        dataType: 'json',
        data: { team_id: team_id },
        success: function (result) {
          var options = '';
          $.each(result, function (key, val) {
            options += '<option value="' + val.id + '" selected>' + val.first_name + ' '+val.last_name+ '</option>';
          });
          $("#filter_learners").html(options);
          $("#filter_learners").multiselect("rebuild");
        }
      });
    }
    else{
      $("#filter_learners").html("");
      $("#filter_learners").multiselect("rebuild");
    }
  }

  $("#filter_learners").multiselect({
    nonSelectedText: $("#filter_learners").data("label"),
    buttonWidth: '100%',
    includeSelectAllOption: true,
    disableIfEmpty: true
  });

  $("#filter_scores").multiselect({
    nonSelectedText: $("#filter_scores").data("label"),
    buttonWidth: '100%',
    onChange: function (option, checked) {
      if(checked)
      {
        var selected = $(option).val();
        if (selected == 'all' || selected == 'average') {
          $('#filter_scores').multiselect('deselect', ['all', 'average']);
          $('#filter_scores').multiselect('select', [selected]);
        }
        if (selected == 'between' || selected == 'lower_than' || selected == 'higher_than')
        {
          $('#filter_scores').multiselect('deselect', ['between', 'lower_than', 'higher_than']);
          $('#filter_scores').multiselect('select', [selected]);
        }
      }
      scoreFilter();
    },
  });

  function scoreFilter()
  {
    $(".score_filter").hide();
    $(".score_filter").each(function(){
      $(this).find("input").val("");
      $(this).hide();
    });
    $('#filter_scores option:selected').each(function () {
        $("#filterscore_" + $(this).val()).show();
    });
  }

  function criteria_score_between() {
    return `
      <div class="col-sm-2">
        <label>`+trans('js.score_between')+`</label>
      </div>
      <div class="col-sm-2">
        <input type="number" class="form-control" id="score_filter" max="100" min="0" step=".1">
      </div>
      <div class="col-sm-1 text-center">
        <label>`+trans('js.and')+`</label>
      </div>
      <div class="col-sm-2">
        <input type="number" class="form-control" id="score_filter_2" max="100" min="0" step=".1">
      </div>
    `;
  }

  function criteria_score_lower() {
    return `
      <div class="col-sm-2">
        <label>`+trans('js.score_lower_than')+`</label>
      </div>
      <div class="col-sm-2">
        <input type="number" class="form-control" id="score_filter" max="100" min="0" step=".1">
      </div>
    `;
  }

  function criteria_score_higher() {
    return `
      <div class="col-sm-2">
        <label>`+trans('js.score_higher_than')+`</label>
      </div>
      <div class="col-sm-2">
        <input type="number" class="form-control" id="score_filter" max="100" min="0" step=".1">
      </div>
    `;
  }


  $(".addcriteria").on("click", function(){
    var type = $(this).data("type");
    $("#criteria_"+type).show();
  })

  $(".rm-criteria").on("click", function(){
    $(this).closest('.row').find('input').val("");
    $(this).closest('.row').fadeOut(1000);
  });


  function getModuleByCourse() {
    var opt = $('#filter_courses option:selected');
    var course_id = [];
    $(opt).each(function (index, brand) {
      course_id.push([$(this).val()]);
    });
    if (course_id.length > 0) {
      $.ajax({
        type: 'GET',
        url: $("#filter_courses").data('url'),
        dataType: 'json',
        data: { course_id: course_id },
        success: function (result) {
          var options = '';
          $.each(result, function (key, val) {
            options += '<option value="' + val.id + '" selected>' + val.title+ '</option>';
          });
          $("#module").html(options);
          $("#module").multiselect("rebuild");
        }
      });
    }
    else {
      $("#module").html("");
      $("#module").multiselect("rebuild");
    }
  }


  function format(item) {
    var originalOption = item.element;
    return '<span><img src="'+ $(originalOption).data('image') + '" width="60"></span> ' + item.text;
  }
  $('.select2icon').select2({
      width: "100%",
      templateResult: format,
      escapeMarkup: function(markup) {
        return markup;
      }
  });


  $('body').on('click', '.clearFilter', function() {
    parent = $(this).closest('.row');
    parent.find('select').val("").trigger("change");
    parent.find('input').val("");
  });

  $('#switchChart').on('change', function () {
    getChartActives()
  });

  var getChartActives = function () {
    var status = $('#switchChart input:checked').val() || 'off';
    if(status == 'on')
    {
      $("#expandChart").prop("disabled", false);
      $("#expandChart").click();
      if ($("#chartProperties").hasClass("d-none"))
        $("#chartProperties").toggleClass("d-none");
    }else{
      if ($("#chartProperties").hasClass("show"))
        $("#chartProperties").toggleClass("show");
      $("#expandChart").prop("disabled", true);
    }
  };

  $('#switchTable').on('change', function () {
    getTableActive()
  });

  var getTableActive = function () {
    var status = $('#switchTable input:checked').val() || 'off';
    if (status == 'on') {
      $("#expandTable").prop("disabled", false);
      $("#expandTable").click();
      if ($("#tableProperties").hasClass("d-none"))
        $("#tableProperties").toggleClass("d-none");
    } else {
      if ($("#tableProperties").hasClass("show"))
        $("#tableProperties").toggleClass("show");
      $("#expandTable").prop("disabled", true);
    }
  };

  $(".sortable").sortable();


  $("#addColumn").on('click', function () {
    var col = $("#columns").val(),
      label = $("#columns option:selected").text();

    if ($("#columns").val() !== '') {
      exists = false;
      $('input[name="columns[]"]').each(function () {
        if (exists == false && $(this).val() == col)
          exists = true;
      })

      if (exists == false)
        addColumn(col, label);
    }
  })
  function addColumn(col, label) {
    $("#columnList").append(`
        <li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center">
          <span>
            <i class="fa fa-arrows-alt-v"></i> `+ label + `
            <input type="hidden" name="columns[]" class="column_name" value="`+ col + `">
          </span>
          <button type="button" class="btn btn-sm btn-light removeColumn"><i class="fa fa-minus"></i></button>
        </li>
    `);
  }

  $("#clearColumn").on('click', function () {
    $("#columnList").html("");
  });

  $('body').on('click', '.removeColumn', function () {
    $(this).closest('li').remove();
  });

  setByDefault();
  function setByDefault()
  {
    $("#columnList").html("");
    var defaultColumns = $("#columnList").data("default").split(";");
    $.each(defaultColumns, function (index, val) {
      $("#columns > option").each(function () {
        if (this.value == val)
          addColumn(val, this.text);
      });
    });
    // generateReport();
  }

  function generateReport()
  {
    if(!$('#collapseReport').hasClass('show'))
      $('#collapseReport').collapse('toggle');


    $("#result").html('<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">'+trans('js.processing')+'...</div></div>');

    var columns = [];
    $('input[name="columns[]"]').each(function () {
      columns.push($(this).val());
    })

    if (columns.length === 0)
      columns = ['learner', 'score', 'completion'];

    $.ajax({
      type: 'POST',
      url: $("#result").data('url'),
      data: {
          '_token': $('meta[name="csrf-token"]').attr('content'),
          'filter_course': $("#filter_courses").val(),
          'filter_team': $("#filter_teams").val(),
          'filter_learner': $("#filter_learners").val(),
          'filter_score': $("#filter_scores").val(),
          'score_between': $("#score_between").val(),
          'score_between_to': $("#score_between_to").val(),
          'score_lower_than': $("#score_lower_than").val(),
          'score_higher_than': $("#score_higher_than").val(),
          'enrolled_date': $("#enrolled_date").val(),
          'enrolled_date': $("#enrolled_date").val(),
          'enrolled_between': $("#enrolled_between").val(),
          'enrolled_between_to': $("#enrolled_between_to").val(),
          'completion': $("#completion").val(),
          'completion_range': $("#completion_range").val(),
          'completion_range_to': $("#completion_range_to").val(),
          'status': $("#status").val(),
          'modules': $("#module").val(),
          'success_status': $("#success_status").val(),
          'completion_status': $("#completion_status").val(),
          'chart_active': $('#switchChart input:checked').val() || 'off',
          'chart_type': $("#chart_type").val(),
          'chart_title': $("#chart_title").val(),
          'chart_subtitle': $("#chart_subtitle").val(),
          'chart_values': $("#chart_values").val(),
          'chart_categories': $("#chart_categories").val(),
          'chart_series': $("#chart_series").val(),
          'table_active': $('#switchTable input:checked').val() || 'off',
          'table_columns': JSON.stringify(columns),
      },
      success: function(result) {
        $("#result").html(result);
      }
    });
  }

  $("#generate").on("click", function(){
    generateReport();
  });

});
