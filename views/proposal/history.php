<?php $config = RPC_Registry::get( 'config' ); ?>
<style type="text/css">
	.rejected, .rejected:hover{
		background-color: #ffa8a85e !important;
	}
	.retracted, .retracted:hover{
		background-color: cornsilk !important;
	}
	.proposal-head {
        font-weight:600;
        font-size:18px;
        margin:25px 0 0 0;
    }
    .btn.btn-magilla-default{
    	color: <?= $config['white_label']['light_txt']; ?> !important;
    }
</style>


<div id="loan_questions_wrapper" class="portlet">
    <div class="portlet-body magchat-container" style="position:relative;">
    	<div  class="col-xs-12" style = "background-color: <?= $config['white_label']['primary_color']; ?>; color: <?= $config['white_label']['light_txt']; ?>; padding-bottom: 3%">
    		<h3 class="proposal-head pull-left">Proposal Status</h3>
    		<a href="#viewProposal" data-lid="<?= $loan_id; ?>" data-action="addAdditionalProposal" class="btn btn-magilla-default pull-right viewProposal " aria-hidden="true" style= "margin-top: 3%; color: <?= $config['white_label']['light_txt']; ?>; border: <?= $config['white_label']['light_txt']; ?> solid 1px">Add Proposal </a>
    	</div>

		<!-- BEGIN PAGE CONTENT BODY -->
		<div id="ProposalDetail" class="page-content">
			
		<!-- BEGIN PAGE CONTENT INNER -->
			<div class="tabbable-custom">
		      
 				
				<table class="magilla-table table table-light table-hover" datagrid="proposals">
					<thead>
						<tr>
							<th class="name-column">Status</th>
							<th class="th-sortable"><sort field="mag_proposal.proposal_added">Submitted</sort></th>
							<th class="th-sortable"><sort field="mag_proposal.proposal_amount">Proposal<br/>Amount</sort></th>
							<th class="th-sortable"><sort field="mag_proposal.proposal_rate">Interest Rate</sort></th>
						</tr>
					</thead>
					<tbody>
						<?php if( $proposals->getRows() ): ?>
							<?php foreach( $proposals->getRows() as $favorite ): ?>
								<?php
									$loan       = $loan_model->load( $favorite['loan_id'] );
									$class      = '';
									$archive  ='';

									if( $favorite['proposal_status'] == 'ACCEPTED' )
									{
										$class .= 'accepted';
									}
									elseif( $favorite['proposal_status'] == 'REJECTED' )
									{
										$class .= 'rejected';
									}
									elseif( $favorite['proposal_status'] == 'RETRACTED' )
									{
										$class .= 'retracted';
									}
									if( $favorite['proposal_archived'] == 'YES' )
									{
										$archive .= ' archived';
									}
									elseif( $favorite['proposal_archived'] == 'NO' )
									{
										$archive .= ' not-archived';
									}
			  
								?>
								<tr class="<?= $class; ?> <?= $archive; ?>">
									<td>
										<a data-action="view" class="viewProposal" href="#" data-pid="<?= $favorite['proposal_id'] ?>">
											<?= $favorite['proposal_status']; ?>
										</a>
									</td>
									<td><?= date( 'm/d/Y @g:iA', strtotime( $favorite['proposal_added'] ) ) ?></td>
									<td>$<?= APP_Util::niceNumber( $favorite['proposal_amount'] ); ?></td>
									<td><?= $favorite['proposal_rate'] ?>%</td>
								</tr>
						  <?php endforeach; ?>
						  <?php else: ?>
						  	 
							  <tr>
								  <td colspan="10">No proposals found.</td>
							  </tr>
						  <?php endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="10">
								<div class="row">
									<pagination>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<!-- END PAGE CONTENT INNER -->
		</div>
	<!-- END PAGE CONTENT BODY -->
	</div>
</div>

<script type="text/javascript">
	
</script>