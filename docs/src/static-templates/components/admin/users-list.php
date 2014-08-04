<div class="large-9 column admin">
	<div class="box admin">
		<div class="row">
			<div class="large-8 column">
				<h2>Users</h2>
			</div>
			<div class="large-4 column">
				<form id="searchform" action="/admin/users" method="post">
					<div style="display:none"><input type="hidden" value="1b00da5bd1809025802ed01476dcdd2baa0a5b26" name="YII_CSRF_TOKEN"></div>
					<div class="row">
						<div class="large-12 column">
							<input type="text" name="search" id="search" placeholder="Enter search query..." value="">
						</div>
					</div>
				</form>
			</div>
		</div>
		<form id="admin_users">
			<table class="grid">
				<thead>
					<tr>
						<th><input type="checkbox" name="selectall" id="selectall"></th>
						<th>ID</th>
						<th>Username</th>
						<th>Title</th>
						<th>First name</th>
						<th>Last name</th>
						<th>Doctor</th>
						<th>Roles</th>
						<th>Active</th>
					</tr>
				</thead>
				<tbody>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="13"></td>
						<td>13</td>
						<td>carrc</td>
						<td>Dr</td>
						<td>Caroline</td>
						<td>Carr</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="21"></td>
						<td>21</td>
						<td>danielc</td>
						<td>Ms</td>
						<td>Claire</td>
						<td>Daniel</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="22"></td>
						<td>22</td>
						<td>danielr</td>
						<td>Mr</td>
						<td>Rhodri</td>
						<td>Daniel</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="23"></td>
						<td>23</td>
						<td>dartj</td>
						<td>Mr</td>
						<td>John</td>
						<td>Dart</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="24"></td>
						<td>24</td>
						<td>davisa</td>
						<td>Miss</td>
						<td>Alison</td>
						<td>Davis</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="25"></td>
						<td>25</td>
						<td>desaip</td>
						<td>Dr</td>
						<td>Parul</td>
						<td>Desai</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="26"></td>
						<td>26</td>
						<td>dowlerj</td>
						<td>Mr</td>
						<td>Jonathan</td>
						<td>Dowler</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="27"></td>
						<td>27</td>
						<td>eganc</td>
						<td>Ms</td>
						<td>Catherine</td>
						<td>Egan</td>
						<td>No</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="28"></td>
						<td>28</td>
						<td>ezrae</td>
						<td>Mr</td>
						<td>Eric</td>
						<td>Ezra</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="29"></td>
						<td>29</td>
						<td>fickerl</td>
						<td>Miss</td>
						<td>Linda</td>
						<td>Ficker</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
					<tr class="clickable">
						<td><input type="checkbox" name="users[]" value="30"></td>
						<td>30</td>
						<td>flanagand</td>
						<td>Mr</td>
						<td>Declan</td>
						<td>Flanagan</td>
						<td>Yes</td>
						<td>-</td>
						<td>Yes</td>
					</tr>
				</tbody>
				<tfoot class="pagination-container">
					<tr>
						<td colspan="9">
							<button class="small primary event-action" type="submit">Add</button>
							<button class="small primary event-action" type="submit">Delete</button>
							<ul class="pagination">
								<li class="first unavailable"><a href="/admin/users">&lt;&lt; First</a></li>
								<li class="previous unavailable"><a href="/admin/users">&lt; Previous</a></li>
								<li class="page current"><a href="/admin/users">1</a></li>
								<li class="page"><a href="/admin/users?page=2">2</a></li>
								<li class="page"><a href="/admin/users?page=3">3</a></li>
								<li class="page"><a href="/admin/users?page=4">4</a></li>
								<li class="page"><a href="/admin/users?page=5">5</a></li>
								<li class="next"><a href="/admin/users?page=2">Next &gt;</a></li>
								<li class="last"><a href="/admin/users?page=5">Last &gt;&gt;</a></li>
							</ul>
						</td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>