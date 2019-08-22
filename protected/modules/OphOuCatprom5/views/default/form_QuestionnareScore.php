<section class="element full edit CatProm5EventResult required">

    <input type="hidden" name="[element_dirty]CatProm5EventResult" value="0">

    <header class="element-header">
        <!-- Add a element remove flag which is used when saving data -->
        <input type="hidden" name="[element_removed]CatProm5EventResult" value="0">
        <!-- Element title -->
        <h3 class="element-title">Questionare Score</h3>
    </header>
    <!-- Additional element title information -->
    <!-- Element actions -->
    <div class="element-actions">
        <!-- order is important for layout because of Flex -->
        <!-- remove MUST be last element -->
        <span class="disabled" title="This is a mandatory element and cannot be closed.">
          <i class="oe-i trash-blue disabled"></i>
        </span>
    </div>

    <div class="element-fields full-width flex-layout">
        <div class="cols-11">

            <div class="flex-layout">
								<input type="hidden" value="" id="CatProm5RawScore" name="CatProm5RawScore">
                <div class="highlighter large-text" id="idg-js-demo-score">0</div>
            </div>
        </div><!-- cols -->
        <script type="text/javascript">
          /*
          Quick JS to demo a score
          */
          document.addEventListener('click', function ( e ) {
            let score = 0;
            if( e.target.type == 'radio'){
              /*
              add up all the scores
              then update the score
              */
              let radios = document.querySelectorAll("fieldset input[type='radio']");
              radios.forEach( function( node ){
                if( node.checked === true ){
                  score += parseInt( node.value );
                }
              });

              let elemScore = document.querySelector('#idg-js-demo-score');
              elemScore.textContent = score;
              $('#CatProm5RawScore').val(score);
            }
          });
        </script>
    </div>

</section>