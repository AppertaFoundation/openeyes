<div class="operation-meta">
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label">Operation(s) Performed:</div>
		</div>
		<div class="large-9 column">
			<ul>
			<?php
				$operations_perf = Element_OphTrOperationnote_ProcedureList::model()->find("event_id = ?", array($this->event->id));
				foreach ($operations_perf->procedures as $procedure) {
					echo "<li>{$operations_perf->eye->name} {$procedure->term}</li>";
				}
			?>
			</ul>
		</div>
	</div>
	<?php
		$surgeon_element = Element_OphTrOperationnote_Surgeon::model()->find("event_id = ?", array($this->event->id));
		$surgeon_name = ($surgeon = User::model()->findByPk($surgeon_element->surgeon_id)) ? $surgeon->getFullNameAndTitle() : "Unknown";
		$assistant_name = ($assistant = User::model()->findByPk($surgeon_element->assistant_id)) ? $assistant->getFullNameAndTitle() : "None";
		$supervising_surg_name = ($supervising_surg = User::model()->findByPk($surgeon_element->supervising_surgeon_id)) ? $supervising_surg->getFullNameAndTitle() : "None";
	?>
	<div class="row data-row surgeons">
		<div class="large-4 column">
			<div class="data-label">First Surgeon</div>
			<div class="data-value"><?php echo $surgeon_name ?></div>
		</div>
		<div class="large-4 column">
			<div class="data-label">Assistant Surgeon</div>
			<div class="data-value"><?php echo $assistant_name ?></div>
		</div>
		<div class="large-4 column">
			<div class="data-label">Supervising surgeon</div>
			<div class="data-value"><?php echo $supervising_surg_name ?></div>
		</div>
	</div>

<!--
<div class="operationMeta">
	<div class="detailRow leftAlign">
		<div class="label">
			Operation(s) Performed:
		</div>
		<div class="value pronounced">
			<?php
			$operations_perf = Element_OphTrOperationnote_ProcedureList::model()->find("event_id = ?", array($this->event->id));
			foreach ($operations_perf->procedures as $procedure) {
				echo "<strong>{$operations_perf->eye->name} {$procedure->term}</strong><br>";
			}
			?>
		</div>
	</div>
	<div class="surgeonList">
		<?php
		$surgeon_element = Element_OphTrOperationnote_Surgeon::model()->find("event_id = ?", array($this->event->id));
		$surgeon_name = ($surgeon = User::model()->findByPk($surgeon_element->surgeon_id)) ? $surgeon->getFullNameAndTitle() : "Unknown";
		$assistant_name = ($assistant = User::model()->findByPk($surgeon_element->assistant_id)) ? $assistant->getFullNameAndTitle() : "None";
		$supervising_surg_name = ($supervising_surg = User::model()->findByPk($surgeon_element->supervising_surgeon_id)) ? $supervising_surg->getFullNameAndTitle() : "None";
		?>
		<div>
			First Surgeon
			<br>
			<strong><?php echo $surgeon_name ?></strong>
		</div>
		<div>
			Assistant Surgeon
			<br>
			<strong><?php echo $assistant_name ?></strong>
		</div>
		<div>
			Supervising surgeon
			<br>
			<strong><?php echo $supervising_surg_name ?></strong>
		</div>
	</div>
</div>
-->