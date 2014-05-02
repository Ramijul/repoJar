
<!--
Author: Ramijul Islam

-->


			</ul>
		</div>
		
		<div id="body">
		
			<div id="form">
				
				<h3>Your Resume</h3>			
				
				<table id="resume">
					<tr>
						<td class="subHeader"><b>Name:</b></td>
						<td><?php echo $name;?></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Phone Number:</b></td>
						<td><?php echo $number;?></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Address:</b></td>
						<td><?php echo $address;?></td>
					</tr>
					
					<tr>
						<td colspan="2"><hr/></td>
					</tr>
					
					<tr>
						<td class="subHeader"><b>Position Sought:</b></td>
						<td><?php echo $position;?></td>
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
								<?php for ($i = 0; $i < $numberofjobs; $i++){?>
								<tr>
									<td class="subHeader">Starting Date:</td>
									<td><?php echo $startdate[$i];?></td>
								</tr>
								
								<tr>
									<td class="subHeader">Ending Date:</td>
									<td><?php echo $enddate[$i];?></td>
								</tr>
								
								<tr>
									<td class="subHeader">Job Description:</td>
									<td><?php echo $text[$i];?></td>
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