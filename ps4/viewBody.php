
<!--
Author: Ramijul Islam

-->

		</div>
		
		<div id="body">
		
			<div id="form">
				
				<h3>Your Resume</h3>			
				
				<span class="errMessage"><?php echo $errMsg;?></span>
				<table class="resume">
					<tr>
						<td class="subHeader"><b>Name:</b></td>
						<td class="details"><?php echo $name;?></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Phone Number:</b></td>
						<td class="details"><?php echo $number;?></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Address:</b></td>
						<td class="details"><?php echo $address;?></td>
					</tr>
					
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Position Sought:</b></td>
						<td class="details"><?php echo $position;?></td>
					</tr>
					
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
					
					<!-- display this section only if experience is provided -->
					<?php if($experience){?>
					<tr>
						<td colspan="2"><b>Employment History:</b></td>
					</tr>
					
					<tr>
						<td colspan="2">
							<table class="resume-emp">
							<!-- repeat this structure for each job set -->
								<?php for ($i = 0; $i < $numOfJobs; $i++){?>
								<tr>
									<td class="subHeader">Starting Date:</td>
									<td class="details"><?php echo $startdate[$i];?></td>
								</tr>
								
								<tr>
									<td class="subHeader">Ending Date:</td>
									<td class="details"><?php echo $enddate[$i];?></td>
								</tr>
								
								<tr>
									<td class="subHeader">Job Description:</td>
									<td class="details"><?php echo $text[$i];?></td>
								</tr>
								
								<tr>
									<td colspan="3"><hr/></td>
								</tr>
								<?php }?> <!-- end for loop -->
							</table>
						</td>
					</tr>
					<?php }?><!-- end if experience -->
				</table>
			</div>			
		</div>
	</div>
	
	</body>
</html>