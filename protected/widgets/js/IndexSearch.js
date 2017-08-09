var show_children = true;
$( document ).ready(function() {
  alert('loadedready');
  (function($) {
    "use strict";
    let opts;
    let search_term;
    $.fn.search = function(options) {
      opts = $.extend({}, $.fn.search.defaults, options);
      let $results = $("#results");
      let $parent = $results.parent();

      this.keyup(function() {
        $results.detach();
        search_term = ($(this).val() + "").toLowerCase();
        for (let selector of opts.selectors) {
          const last_level = opts.selectors[0] == selector;
          $results.find(selector).each(function() {
            let $this = $(this);
            let $element = get_element($this);
            const allias = $this.data("allias").toLowerCase();
            if (allias.indexOf(search_term) == -1) {
              $this.html($this.text());
              if (!last_level && $element.children().find("li[style!='display: none;']").length != 0) {
                $element.show();
              } else {
                $element.hide();
              }
            } else {
              const highlighted_string = replace_matched_string($this.text(), search_term);
              $this.html(highlighted_string);
              $element.show();
              if (!last_level) {
                if (show_children == true) {
                  $element.children().find("li[style='display: none;']").show();
                }
              }
            }
          });
        }
        $results.find('.allias').each(function(){
          let $this = $(this);
          const highlighted_alliases = replace_matched_string($this.text(), search_term);
          $this.html(highlighted_alliases);
        });
        $parent.append($results);
      });
      //can optimise this in future

      return this;
    };
    $.fn.search.defaults = {
      selectors: [".lvl3", ".lvl2", ".lvl1"],
      ancestor_to_change: 2,
      matched_string_tag: ["<em class='search_highlight'>", "</em>"]
    };

    function replace_matched_string(old_string, search_term) {
      if (search_term === undefined || search_term === "" || old_string.toLowerCase().indexOf(search_term.toLowerCase()) == -1) {
        return old_string;
      }
      if (old_string === "") {
        return "";
      }
      const match_start = old_string.toLowerCase().indexOf("" + search_term.toLowerCase() + "");
      const match_end = match_start + search_term.length - 1;
      const before_match = old_string.slice(0, match_start);
      const match_text = old_string.slice(match_start, match_end + 1);
      const after_match = old_string.slice(match_end + 1);
      const new_string = before_match + opts.matched_string_tag[0] + match_text + opts.matched_string_tag[1] + replace_matched_string(after_match, search_term);
      return new_string;
    }

    function get_element($this) {
      for (let i = 0; i < opts.ancestor_to_change; i++) {
        $this = $this.parent();
      }
      return $this;
    }
  }(jQuery));
  $("#search_bar_right").search();
  $("#search_bar_left").search();
  $("#search_bar_right").focus(function(){
    $('#search_bar_left').val('');
    last_search_pos = "right";
    $('#search_bar_right').trigger("keyup");
    show_results();
  });
  $("#search_bar_left").focus(function(){
    $('#search_bar_right').val('');
    last_search_pos = "left";
    $('#search_bar_left').trigger("keyup");
    show_results();
  });


  $('.result_item, .result_item_with_icon').click(function(){
    let $this = $(this);
    let $span = $this.find("span:first");
    let lvl = $span.attr("class");
    switch (lvl) {
      case "lvl1":
      click_lvl_1($this);
      break;
      case "lvl2":
      click_lvl_2($this);
      break;
      case "lvl3":
      click_lvl_3($this);
      break;
      default:
    }
    hide_results();
  });
  /* New event handler for multiple goto in one button*/
  $('.result_item, .result_item_with_icon').click(function(){
    let $this = $(this);
    if ($this.data('elementId')) {
      click_lvl_1($this);
    }
    else if ($this.data('doodleClassName')) {
      click_lvl_2($this);
    }
    else if ($this.data('property')) {
      click_lvl_3($this);
    }
    else {
      alert('No action in configuration');
    }
    hide_results();
  });



  function get_element_name($this){
    return $this.find("span:first").text();
  }
  function click_lvl_1($this, callback) {
    let btn_name = name_on_btn[name];
    let $item = $(".oe-event-sidebar-edit li a:contains("+btn_name+")");
    event_sidebar.loadClickedItem($item,{},callback);
  }
  function click_lvl_2($this, callback){
    let name = get_element_name($this);
    let $parent = $this.parent().parent().parent();
    let parent_name = get_element_name($parent);
    click_lvl_1($parent,function(){
      setTimeout (function(){
        let $lvl_2_item = get_doodle_button(parent_name,name,last_search_pos);
        let $selected_doodle = $("#eyedrawwidget_"+last_search_pos+"_315").find("#ed_example_selected_doodle").children().find("option:contains('"+name+"')");
        if ($selected_doodle.length == 0) {
          $lvl_2_item.trigger("click");
          if (typeof(callback) == "function") {
            callback();
          }
        } else {
          $("#eyedrawwidget_"+last_search_pos+"_315").find("#ed_example_selected_doodle").children().find("option").removeAttr('selected');
          $selected_doodle.attr('selected','selected');
          $("#eyedrawwidget_"+last_search_pos+"_315").find("#ed_example_selected_doodle").trigger('change');
          if (typeof(callback) == "function") {
            callback();
          }
        } //move callback to after if else
      },1000);
    });
  }
  function get_doodle_button(parent_name, name, position) {
    let canvas_id = lvl_1_to_section_id[parent_name];
    let doodle_id = lvl_2_to_doodle_id[name];
    let canvas_doodle_id = "#"+doodle_id+position+"_"+canvas_id;
    let $item = $(canvas_doodle_id).children();
    return $item;
  }
  function click_lvl_3($this, callback){
    //see if popup exists else select it on select box
    let name = get_element_name($this);
    $parent = $this.parent().parent().parent();
    $grand_parent = $parent.parent().parent().parent();
    let parent_name = get_element_name($parent);
    let grand_parent_name = get_element_name($grand_parent);
    click_lvl_2($parent,function(){
      let control_id = get_controls_id(grand_parent_name,last_search_pos);
      $(control_id).find("div:contains("+name+")").effect("highlight", {}, 6000);
    });
  }

  function get_controls_id(name,position){
    //change to concatanation
    return (position == "right" ? "#ed_canvas_edit_right_315_controls": "#ed_canvas_edit_left_315_controls");
  }


  var last_search_pos;
  let name_on_btn= {'Examination Anterior Segment':'Anterior Segment'};
  var lvl_1_to_section_id = {'Examination Anterior Segment' : 315};
  var lvl_2_to_doodle_id = {
    'Adenoviral keratitis' : 'AdenoviralKeratitis',
    'Conjunctivitis' : 'Conjunctivitis',
    'Corneal epithelial defect' : 'CornealEpithelialDefect',
    'Corneal laceration' : 'CornealLaceration',
    'Corneal opacity' : 'CornealOpacity',
    'Corneal pigmentation' : 'CornealPigmentation',
    'Dendritic ulcer' : 'DendriticUlcer',
    'Marginal keratitis' : 'MarginalKeratitis',
    'Pingueculum' : 'Pingueculum',
    'Pterygium' : 'Pterygium',
    'SPEE' : 'SPEE',
    'Metallic foreign body' : 'MetallicForeignBody',

    'No description available for this doodle' : 'NONE',
    'Anterior chamber IOL' : 'ACIOL',
    'AC maintainer' : 'ACMaintainer',
    'Adnexal eye template' : 'AdnexalEye',
    'Angle grade' : 'AngleGrade',
    'Angle Grade East' : 'AngleGradeEast',
    'Angle Grade North' : 'AngleGradeNorth',
    'Angle Grade South' : 'AngleGradeSouth',
    'Angle Grade West' : 'AngleGradeWest',
    'Angle new vessels' : 'AngleNV',
    'Angle recession' : 'AngleRecession',
    'Anterior PVR' : 'AntPVR',
    'Anterior segment' : 'AntSeg',
    'Anterior synechiae' : 'AntSynech',
    'A pattern' : 'APattern',
    'Arcuate keratotomy' : 'ArcuateKeratotomy',
    'Arcuate scotoma' : 'ArcuateScotoma',
    'Arrow' : 'Arrow',
    'Biopsy site' : 'BiopsySite',
    'Trabeculectomy bleb' : 'Bleb',
    'Blot haemorrhage' : 'BlotHaemorrhage',
    'Buckle' : 'Buckle',
    'Buckle operation' : 'BuckleOperation',
    'Buckle suture' : 'BuckleSuture',
    'Busacca nodule' : 'BusaccaNodule',
    'Capsular Tension Ring' : 'CapsularTensionRing',
    'Double chandelier' : 'ChandelierDouble',
    'Chandelier' : 'ChandelierSingle',
    'Choroidal haemorrhage' : 'ChoroidalHaemorrhage',
    'Choroidal naevus' : 'ChoroidalNaevus',
    'Cilary injection' : 'CiliaryInjection',
    'Circinate retinopathy' : 'Circinate',
    'Circumferential buckle' : 'CircumferentialBuckle',
    'Choroidal new vessels' : 'CNV',
    'Conjunctival flap' : 'ConjunctivalFlap',
    'Conjunctival suture' : 'ConjunctivalSuture',
    'Corneal abrasion' : 'CornealAbrasion',
    'Removal of corneal epithelium' : 'CornealErosion',
    'Corneal graft' : 'CornealGraft',
    'Corneal inlay' : 'CornealInlay',
    'Corneal oedema' : 'CornealOedema',
    'Corneal scar' : 'CornealScar',
    'Corneal striae' : 'CornealStriae',
    'Corneal suture' : 'CornealSuture',
    'Cortical cataract' : 'CorticalCataract',
    'Cotton wool spot' : 'CottonWoolSpot',
    'Cryotherapy scar' : 'Cryo',
    'Cutter iridectomy' : 'CutterPI',
    'Cystoid macular oedema' : 'CystoidMacularOedema',
    'Diabetic new vessels' : 'DiabeticNV',
    'Dialysis' : 'Dialysis',
    'Disc haemorrhage' : 'DiscHaemorrhage',
    'Disc pallor' : 'DiscPallor',
    'Drainage retinotomy' : 'DrainageRetinotomy',
    'Drainage site' : 'DrainageSite',
    'Encircling band' : 'EncirclingBand',
    'Entry site break' : 'EntrySiteBreak',
    'Epiretinal membrane' : 'EpiretinalMembrane',
    'Episcleritis' : 'Episcleritis',
    'Fibrous proliferation' : 'FibrousProliferation',
    'Fibrovascular Scar' : 'FibrovascularScar',
    'Focal laser' : 'FocalLaser',
    'Freehand drawing' : 'Freehand',
    'Guttata' : 'Fuchs',
    'Fundus' : 'Fundus',
    'Geographic atrophy' : 'Geographic',
    'Gonioscopy' : 'Gonioscopy',
    'Giant retinal tear' : 'GRT',
    'Hard drusen' : 'HardDrusen',
    'Hard exudate' : 'HardExudate',
    'Hyphaema' : 'Hyphaema',
    'Hypopyon' : 'Hypopyon',
    'IatrogenicBreak' : 'IatrogenicBreak',
    'ILM peel' : 'ILMPeel',
    'Implantable Collamer Lens' : 'ICL',
    'Intraocular lens' : 'IOL',
    'Injection site' : 'InjectionSite',
    'Inner leaf break' : 'InnerLeafBreak',
    'Iris' : 'Iris',
    'Iris hook' : 'IrisHook',
    'Iris naevus' : 'IrisNaevus',
    'Intraretinal microvascular abnormalities' : 'IRMA',
    'Keratic precipitates' : 'KeraticPrecipitates',
    'Koeppe nodule' : 'KoeppeNodule',
    'Krukenberg spindle' : 'KrukenbergSpindle',
    'Label' : 'Label',
    'Circle of laser photocoagulation' : 'LaserCircle',
    'Laser demarcation' : 'LaserDemarcation',
    'LASIK flap' : 'LasikFlap',
    'Laser spot' : 'LaserSpot',
    'Lattice' : 'Lattice',
    'Lens' : 'Lens',
    'Limbal relaxing incision' : 'LimbalRelaxingIncision',
    'Macroaneurysm' : 'Macroaneurysm',
    'Macular dystrophy' : 'MacularDystrophy',
    'Macular grid laser' : 'MacularGrid',
    'Macular hole' : 'MacularHole',
    'Macular thickening' : 'MacularThickening',
    'Malyugin ring' : 'Malyugin',
    'Mattress suture' : 'MattressSuture',
    'Microaneurysm' : 'Microaneurysm',
    'Nerve fibre defect' : 'NerveFibreDefect',
    'Nuclear cataract' : 'NuclearCataract',
    'Optic cup' : 'OpticCup',
    'Optic disc' : 'OpticDisc',
    'Optic disc pit' : 'OpticDiscPit',
    'Orthoptic eye' : 'OrthopticEye',
    'Outer leaf break' : 'OuterLeafBreak',
    'Papilloedema' : 'Papilloedema',
    'Tube patch' : 'Patch',
    'Posterior chamber IOL' : 'PCIOL',
    'Peripapillary atrophy' : 'PeripapillaryAtrophy',
    'Peripheral retinectomy' : 'PeripheralRetinectomy',
    'Phako incision' : 'PhakoIncision',
    'Peripheral iridectomy' : 'PI',
    'Point in line' : 'PointInLine',
    'Posterior capsule' : 'PosteriorCapsule',
    'Posterior embryotoxon' : 'PosteriorEmbryotoxon',
    'Posterior pole' : 'PostPole',
    'Posterior subcapsular cataract' : 'PostSubcapCataract',
    'Posterior retinectomy' : 'PosteriorRetinectomy',
    'Posterior synechia' : 'PosteriorSynechia',
    'Pre-retinal haemorrhage' : 'PreRetinalHaemorrhage',
    'Panretinal photocoagulation' : 'PRP',
    'Panretinal photocoagulation (posterior pole)' : 'PRPPostPole',
    'Phototherapeutic keratectomy' : 'PTK',
    'Pupil' : 'Pupil',
    'Radial sponge' : 'RadialSponge',
    'Retinal artery occlusion' : 'RetinalArteryOcclusionPostPole',
    'Retinal haemorrhage' : 'RetinalHaemorrhage',
    'Retinal touch' : 'RetinalTouch',
    'Retinal vein occluson' : 'RetinalVeinOcclusionPostPole',
    'Retinoschisis' : 'Retinoschisis',
    'Radial keratotomy' : 'RK',
    'Round hole' : 'RoundHole',
    'RPE Atrophy' : 'RPEAtrophy',
    'RPE detachment' : 'RPEDetachment',
    'RPE Hypertrophy' : 'RPEHypertrophy',
    'RPE rip' : 'RPERip',
    'Rhegmatogenous retinal detachment' : 'RRD',
    'Rubeosis iridis' : 'Rubeosis',
    'Sector PRP' : 'SectorPRP',
    'Sector PRP (posterior pole)' : 'SectorPRPPostPole',
    'Scleral Incision' : 'ScleralIncision',
    'Sclerostomy' : 'Sclerostomy',
    'Sector iridectomy' : 'SectorIridectomy',
    'Shading' : 'Shading',
    'Side port' : 'SidePort',
    'Slider' : 'Slider',
    'Small incision lenticule extraction' : 'SMILE',
    'Star fold' : 'StarFold',
    'Subretinal fluid' : 'SubretinalFluid',
    'Subretinal PFCL' : 'SubretinalPFCL',
    'Supramid suture' : 'Supramid',
    'Swollen disc' : 'SwollenDisc',
    'Parafoveal telangiectasia' : 'Telangiectasis',
    'Trabectome' : 'Trabectome',
    'Trabeculectomy conjunctival incision' : 'TrabyConjIncision',
    'Trabeculectomy flap' : 'TrabyFlap',
    'Trabeculectomy suture' : 'TrabySuture',
    'Toric posterior chamber IOL' : 'ToricPCIOL',
    'Traction retinal detachment' : 'TractionRetinalDetachment',
    'Transillumination defect' : 'TransilluminationDefect',
    'Drainage tube' : 'Tube',
    'Tube extender' : 'TubeExtender',
    'Ligation suture' : 'TubeLigation',
    'Up drift' : 'UpDrift',
    'Up shoot' : 'UpShoot',
    'Traction ‘U’ tear' : 'UTear',
    'Vicryl suture' : 'Vicryl',
    'View obscured' : 'ViewObscured',
    'Vitreous opacity' : 'VitreousOpacity',
    'V pattern' : 'VPattern',
    'Crepitations' : 'Crepitations',
    'Stenosis' : 'Stenosis',
    'Wheeze' : 'Wheeze',
    'Pleural effusion' : 'Effusion',
    'Left coronary artery' : 'LeftCoronaryArtery',
    'Drug eluting stent' : 'DrugStent',
    'Metal stent' : 'MetalStent',
    'Coronary artery bypass' : 'Bypass',
    'Bruit' : 'Bruit',
    'Bruising' : 'Bruising',
    'Haematoma' : 'Haematoma',
  }

  $('#big_cross').click(function(){
    hide_results();
  });

  $('body').append('<div id="dim_rest" class="ui-widget-overlay" style="display : none; width: 1280px; height: 835px; z-index: 100;"></div>');
  $(document).ready(function(){
    $('#description_toggle').change(function(){
      if (this.checked) {
        $('.description_icon').show();
        $('.description_note').show();
      } else {
        $('.description_icon').hide();
        $('.description_note').hide();
      }
      event.stopPropagation();
    });
    $('#children_toggle').change(function(){
      let current_search_bar = last_search_pos == "right" ? "#search_bar_right" : "#search_bar_left";
      if (this.checked) {
        show_children = true;
        $(current_search_bar).trigger('keyup');
      } else {
        show_children = false;
        $(current_search_bar).trigger('keyup');
      }
      event.stopPropagation();
    });

    $(window).click(function() {
      hide_results();
    });

    $('.switch').click(function(){
      event.stopPropagation();
    });

    $('#results').click(function(){
      event.stopPropagation();
    });
  });

  $('#search_bar_right,#search_bar_left').click(function(){
    event.stopPropagation();
  });

  function show_results(){
    var body = document.body,
    html = document.documentElement;
    var height = Math.max( body.scrollHeight, body.offsetHeight,
      html.clientHeight, html.scrollHeight, html.offsetHeight );
      $('#dim_rest').css("height", height);
      $('#dim_rest').show();
      $("body").css("overflow","hidden");
      $("#results").show();
      $(".switch").show();
      $("#description_toggle_label,#children_toggle_label,#search_options").show();
    }
    function hide_results(){
      $('#search_bar_right,#search_bar_left').val('');
      $('#results').scrollTop(0);
      $('#dim_rest').hide();
      $("body").css("overflow","auto");
      $("#results").hide();
      $(".switch").hide();
      $("#description_toggle_label,#children_toggle_label,#search_options").hide();
    }
});
