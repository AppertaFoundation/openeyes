
        <div class="large-8 column">
			<div class="row field-row">
				<div class="large-2 column">
					<span class="field-info">Lens name:</span>
				</div>
				<div class="large-6 column">
					<span><?php echo $iolRefValues->lens->name; ?></span>
				</div>
            </div>
            <div class="row field-row">
				<div class="large-2 column">
					<span class="field-info">Formula name:</span>
				</div>
				<div class="large-6 column">
                    <span><?php echo $iolRefValues->formula->name; ?></span>
				</div>
			</div>
            <div class="row field-row">
                <div class="large-2 column">
                    <span class="field-info">A const:</span>
                </div>
                <div class="large-6 column">
                    <span><?php echo $iolRefValues->lens->acon; ?></span>
                </div>
            </div>

            <div class="row field-row">
                <div class="large-2 column">
                    <span class="field-info">Emmetropia:</span>
                </div>
                <div class="large-6 column">
                    <span><?php echo $iolRefValues->{"emmetropia_$side"}; ?></span>
                </div>
            </div>

        </div>
        <div class="large-4 column">
            <div class="row field-row">
                <div class="large-4 column">
                    <table>
                        <tr>
                            <th>IOL</th>
                            <th>REF</th>
                        </tr>
                        <?php
                            $iolData = json_decode($iolRefValues->{"iol_ref_values_$side"}, true);
                            for($i= 0; $i< count($iolData["IOL"]); $i++){
                                if($i == 3){
                                    echo "<tr><td><b>".$iolData["IOL"][$i]."</b></td><td><b>".$iolData["REF"][$i]."</b></td></tr>";
                                }else {
                                    echo "<tr><td>" . $iolData["IOL"][$i] . "</td><td>" . $iolData["REF"][$i] . "</td></tr>";
                                }
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>