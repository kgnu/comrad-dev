<?php $this->Html->script('scheduled_events/add.js', array('inline' => false)); ?>

<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Add Scheduled Event</h1>
		</div>
		<?php echo $this->Form->create(false, array('type' => 'post', 'class' => 'form-horizontal')); ?>
			<fieldset id="scheduled-event">
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('ScheduledEvent.se_EventId', array(
							'label' => 'Event',
							'empty' => true,
							'options' => $events
						)); ?>
						<?php echo $this->TB->input('ScheduledEvent.se_RecordingOffset', array(
							'label' => 'Recording Offset (min)',
							'type' => 'text',
							'pattern' => '[0-9]*',
							'class' => 'input-large'
						)); ?>
					</div>
				</div>
			</fieldset>
			<fieldset id="time-info">
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('TimeInfo.ti_StartDateTime', array(
							'label' => 'Start Date',
							'type' => 'datetime',
							'class' => 'input-small'
						)); ?>
						<?php echo $this->TB->input('TimeInfo.ti_Duration', array(
							'label' => 'Duration (min)',
							'type' => 'text',
							'pattern' => '[0-9]*',
							'class' => 'input-small'
						)); ?>
						<?php echo $this->TB->input('TimeInfo.ti_DISCRIMINATOR', array(
							'label' => 'Repeats',
							'options' => array(
								'NonRepeatingTimeInfo' => 'Never',
								'DailyRepeatingTimeInfo' => 'Daily',
								'WeeklyRepeatingTimeInfo' => 'Weekly',
								// 'MonthlyRepeatingTimeInfo' => 'Monthly',
								// 'YearlyRepeatingTimeInfo' => 'Yearly'
							)
						)); ?>
					</div>
				</div>
				<div class="row-fluid repeat weekly">
					<div class="span12">
						<div class="control-group">
							<label class="control-label">On Days</label>
							<div class="controls">
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnSunday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Sunday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnMonday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Monday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnTuesday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Tuesday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnWednesday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Wednesday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnThursday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Thursday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnFriday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Friday
								</label>
								<label class="checkbox">
									<?php echo $this->TB->basic_input('TimeInfo.ti_WeeklyOnSaturday', array(
										'type' => 'checkbox',
										'label' => false
									)); ?> Saturday
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid repeat">
					<div class="span12">
						<?php echo $this->TB->input('TimeInfo.ti_Interval', array(
							'label' => 'Repeats Every',
							'type' => 'text',
							'pattern' => '[0-9]*',
							'class' => 'input-mini',
							'help_inline' => '<span class="interval-help"></span>'
						)); ?>
						<?php echo $this->TB->input('TimeInfo.ti_EndDate', array(
							'label' => 'End Date',
							'type' => 'date',
							'class' => 'input-small'
						)); ?>
					</div>
				</div>
			<fieldset>
				<div class="form-actions">
					<?php echo $this->TB->button('Save Scheduled Event', array('style' => 'primary')); ?>
					<?php echo $this->Html->link('Cancel', $this->request->referer(), array('class' => 'btn')); ?>
				</div>
			</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
