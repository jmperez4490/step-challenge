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
			<th>Status</th>
			<th>Steps Completed</th>
		</tr>
		<?php
			foreach ($value as $name => $steps) {
	?>
			<tr>
				<td><?=$steps['first_name']?></td>
				<td><div style = "width:<?=number_format($steps['steps']/$steps['goal'],3)*100?>%"></div></td>
				<td><?=$steps['steps']?></td>
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
			<td><?=$team?></td><td><div style = "width:<?=$status?>%;background: <?=$team?>;"></div></td></tr>
	<?php
		}
	?>
		</tr>
	<?php
		}
	?>
	</table>
</div>