<?php 
$uri = $_SERVER['REQUEST_URI']; 
$array = explode('/', $uri);
$key = array_search("pages", $array);
$name = $array[$key + 1];

require_once('connect.php');
$sql_noti = "SELECT * FROM tb_noti WHERE active = 'true' ORDER BY created_at DESC LIMIT 5";
$result_noti = $conn->query($sql_noti);

$sql_updated = "
SELECT
time_stamp 

FROM
aggregated_data_2024

LIMIT 1
";

$result_updated = $conn->query( $sql_updated );
?>

<div class="page-header navbar navbar-fixed-top">
	<div class="page-header-inner ">
		<!-- logo start -->
		<div class="page-logo">
			<a href="../">
				<span class="logo-icon material-icons ">assessment</span>
				<span class="logo-default">IDSS</span> </a>
		</div>
		<!-- logo end -->
		<form class="search-form-opened" action="#" method="GET">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Search..." name="query">
				<span class="input-group-btn">
					<a href="javascript:;" class="btn submit">
						<i class="icon-magnifier"></i>
					</a>
				</span>
			</div>
		</form>
		<!-- start mobile menu -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
			<span></span>
		</a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<li><a class="fullscreen-btn"><i data-feather="maximize"></i></a></li>

				<!-- start notification dropdown -->
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
					<a href="javascript:;" class="dropdown-toggle" data-bs-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i data-feather="bell"></i>
						<span class="badge headerBadgeColor1"> <?php echo $result_noti->num_rows; ?> </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3><span class="bold">Notifications</span></h3>

						</li>
						<li>
							<ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
								<?php 
                                      $num = 0;
                                      while($row = $result_noti->fetch_assoc()){
                                        $num++;
                                    ?>
								<li>
									<a href="javascript:;">
										<span class="time"><?php echo date("d/m/Y H:i:s", strtotime($row['created_at'])); ?></span>
										<?php 
                // Check if noti_group is 1
                if ($row['noti_group'] == 1) {
                    echo '<span class="notification-icon circle yellow"><i class="fa fa-warning"></i></span>';
                }
                // Check if noti_group is 2
                elseif ($row['noti_group'] == 2) {
                    echo '<span class="notification-icon circle purple-bgcolor"><i class="fa fa-user o"></i></span>';
                }
                // For other noti_group values
                else {
                    echo '<span class="notification-icon circle red"><i class="fa fa-times"></i></span>';
                }
            ?>
										<?php echo $row['noti_detail']; ?>
									</a>
								</li>
								<?php } ?>

							</ul>
							<div class="dropdown-menu-footer">
								<a href="javascript:void(0)"> All notifications </a>
							</div>
						</li>
					</ul>
				</li>
				<!-- end notification dropdown -->
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<!-- space !!! -->
			</ul>
		</div>
	</div>


	<div class="navbar-custom">
		<div class="hor-menu hidden-sm hidden-xs">
			<ul class="nav navbar-nav">
				<li class="mega-menu-dropdown <?php if ($name == 'CPI_X_1' || $name == 'CPI_X_2' || $name == 'CPI_X_3' || $name == 'CPI_X_4' || $name == 'CPI_X_5' || $name == 'CPI_X_6' || $name == 'CPI_X_7' || $name == 'CPI_X_8') { echo 'active open'; } ?>">
					<a href="" class="dropdown-toggle"> <i data-feather="airplay"></i> การบริหารค่าใช้จ่าย CPI-X
						<i class="fa fa-angle-down"></i>
						<span class="arrow "></span>
					</a>
					<ul class="dropdown-menu" style="min-width: 200px;">
						<li>
							<div class="mega-menu-content">
								<div class="row">
									<div class="col-md-12">
										<ul class="mega-menu-submenu">
											<li class="<?php echo $name == 'CPI_X' ? 'active': '' ?>">
												<a href="../CPI_X" class="nav-link "> <span class="title">1. สรุปภาพรวม</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_1' ? 'active': '' ?>">
												<a href="../CPI_X_1" class="nav-link "> <span class="title">2. สรุปข้อมูล กฟส.(L,M)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_2' ? 'active': '' ?>">
												<a href="../CPI_X_2" class="nav-link "> <span class="title">3. เปรียบเทียบ กฟส.(L,M)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_3' ? 'active': '' ?>">
												<a href="../CPI_X_3" class="nav-link "><span class="title">4. สรุปข้อมูล กฟส.(S)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_4' ? 'active': '' ?>">
												<a href="../CPI_X_4" class="nav-link "><span class="title">5. เปรียบเทียบ กฟส.(S)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_5' ? 'active': '' ?>">
												<a href="../CPI_X_5" class="nav-link "><span class="title">6. สรุปข้อมูล กฟส.(XS)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_6' ? 'active': '' ?>">
												<a href="../CPI_X_6" class="nav-link "><span class="title">7. เปรียบเทียบ กฟส.(XS)</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_7' ? 'active': '' ?>">
												<a href="../CPI_X_7" class="nav-link "><span class="title">8. ค้นหาหน่วยงาน</span></a>
											</li>
											<li class="<?php echo $name == 'CPI_X_8' ? 'active': '' ?>">
												<a href="../CPI_X_8" class="nav-link "><span class="title">9. สรุปข้อมูลตามรหัสบัญชี</span></a>
											</li>

											(ข้อมูลวันที่
											<?php 
if ($result_updated ->num_rows > 0) {
    $row = $result_updated ->fetch_assoc();
    $create_at = $row["time_stamp"];
    
    // Convert to Thai year
    $date = new DateTime($create_at);
    $thai_year = $date->format('Y') + 543;
    $thai_date = $date->format('d/m/') . $thai_year;

    echo ": " . $thai_date . "";
} else {
    echo "0 results";
}
 ?>)
										</ul>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</li>


				<!--
                <li class="nav-item <?php //echo $name == 'data1' ? 'active': '' ?> ">
                    <a href="../data1" class="nav-link nav-toggle"> <i data-feather="grid"></i>
                        <span class="title">ข้อมูลตามรหัสบัญชี</span><span class="selected"></span>
                    </a>
                </li>
-->

			</ul>
		</div>
	</div>
</div>



<!-- start sidebar menu -->
<div class="sidebar-container">
	<div class="sidemenu-container navbar-collapse collapse fixed-menu">
		<div id="remove-scroll" class="left-sidemenu">
			<ul class="sidemenu  page-header-fixed slimscroll-style" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
				<li class="sidebar-toggler-wrapper hide">
					<div class="sidebar-toggler">
						<span></span>
					</div>
				</li>

				<li class="nav-item start <?php if ($name == 'CPI_X_1' || $name == 'CPI_X_2' || $name == 'CPI_X_3' || $name == 'CPI_X_4' || $name == 'CPI_X_5' || $name == 'CPI_X_6' || $name == 'CPI_X_7' || $name == 'CPI_X_8') { echo 'active open'; } ?>">
					<a href="#" class="nav-link nav-toggle">
						<i data-feather="airplay"></i>
						<span class="title">การบริหารค่าใช้จ่าย CPI-X</span>
						<span class="selected"></span>
						<span class="arrow open"></span>
					</a>
					<ul class="sub-menu">
						<li class="nav-item  <?php echo $name == 'CPI_X' ? 'active': '' ?> ">
							<a href="../CPI_X" class="nav-link ">
								<span class="title">1. สรุปภาพรวม</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_1' ? 'active': '' ?>">
							<a href="../CPI_X_1" class="nav-link ">
								<span class="title">2. สรุปข้อมูล กฟส.(L,M)</span>
								<span class="selected"></span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_2' ? 'active': '' ?>">
							<a href="../CPI_X_2" class="nav-link ">
								<span class="title">3. เปรียบเทียบ กฟส.(L,M)</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_3' ? 'active': '' ?>">
							<a href="../CPI_X_3" class="nav-link ">
								<span class="title">4. สรุปข้อมูล กฟส.(S)</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_4' ? 'active': '' ?>">
							<a href="../CPI_X_4" class="nav-link ">
								<span class="title">5. เปรียบเทียบ กฟส.(S)</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_5' ? 'active': '' ?>">
							<a href="../CPI_X_5" class="nav-link ">
								<span class="title">6. สรุปข้อมูล กฟส.(XS)</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_6' ? 'active': '' ?>">
							<a href="../CPI_X_6" class="nav-link ">
								<span class="title">7. เปรียบเทียบ กฟส.(XS)</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_7' ? 'active': '' ?>">
							<a href="../CPI_X_7" class="nav-link ">
								<span class="title">8. ค้นหาหน่วยงาน</span>
							</a>
						</li>
						<li class="nav-item <?php echo $name == 'CPI_X_8' ? 'active': '' ?>">
							<a href="../CPI_X_8" class="nav-link ">
								<span class="title">9. สรุปข้อมูลตามรหัสบัญชี</span>
							</a>
						</li>
						<li>(ข้อมูลวันที่ <?php echo ": " . $thai_date . "";?> )</li>
					</ul>
				</li>


			</ul>
		</div>
	</div>
</div>
<!-- end sidebar menu -->