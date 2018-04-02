<div class = "score-board">
	<table>
		<tr>
			<th colspan="3">Team Status</th>
		</tr>
	<?php
		foreach ($api -> teamStatus() as $key => $value) {
	?>
		<tr>
			<th colspan="3">Week <?=$key?></th>
		</tr>

		<tr>
			<th>Teammate's Name</th>
			<th>Completed Steps</th>
			<th>Required Steps</th>
		</tr>
		<?php
			foreach ($value as $name => $steps) {
	?>
			<tr>
				<td><?=$steps['first_name']?></td>
				<td><?=number_format($steps['steps'])?></td>
				<td><?=number_format($steps['goal'])?></td>
			</tr>
	<?php
			}
		?>
	<?php
		}
	?>
		</tr>
	</table>

	<table>
		<tr>
			<th colspan="3">Score Board</th>
		</tr>
		<tr>
			<th>Week</th><th>Team</th><th>Status</th>
		</tr>
	<?php
		foreach ($api -> competitorStatus() as $key => $value) {
	?>
		<tr>
			<td rowspan="<?=count($value)?>"><?=$key?></td>
	<?php
		foreach($value as $team => $status)
		{
	?>
			<td><?=$team?></td><td><?=$status?></td></tr>
	<?php
		}
	?>
		</tr>
	<?php
		}
	?>
	</table>
</div>