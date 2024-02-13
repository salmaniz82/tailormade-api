import { Link } from "react-router-dom";

export default function Sidebar() {
  return (
    <aside className="aside-sm bg-dark">
      <a href="/" className="nav-brand" data-toggle="fullscreen">
        Tailormade
      </a>

      <ul id="top-menu" className="pl-10 pt-50">
        <li>
          <span className="material-icons-outlined">dashboard</span>
          <Link to="/">Swatches</Link>
        </li>

        <li>
          <span className="material-icons-outlined">playlist_add</span>
          <Link to="/addswatch">Add swatch</Link>
        </li>

        <li>
          <span className="material-icons-outlined">inventory</span>
          <Link to="/collections">Collections</Link>
        </li>

        <li>
          <span className="material-icons-outlined t-red">logout</span>
          <a href="/logout">Logout</a>
        </li>

        {false && (
          <>
            <li>
              <span className="material-icons-outlined">memory</span>
              <Link to="/demo">Demo</Link>
            </li>

            <li>
              <span className="material-icons-outlined">memory</span>
              <Link to="/thismustgoto404"> Test 404</Link>
            </li>

            <li>
              <span className="material-icons-outlined">perm_media</span>
              <a href="/dashboard/media">Media</a>
            </li>

            <li>
              <span className="material-icons-outlined">category</span>
              <a href="/dashboard/categories">Categories</a>
            </li>

            <li>
              <span className="material-icons-outlined">checklist</span>
              <a href="/dashboard/todos">TODOS</a>
            </li>

            <li>
              <span className="material-icons-outlined">pages</span>
              <a href="/dashboard/pages">Pages</a>
            </li>

            <li>
              <span className="material-icons-outlined">article</span>
              <a href="/dashboard/blogs">Blogs</a>
            </li>

            <li>
              <span className="material-icons">widgets</span>
              <a href="#about">Menus</a>
            </li>

            <li>
              <span className="material-icons-outlined">inventory_2</span>
              <a href="#mission">Products</a>
            </li>

            <li>
              <span className="material-icons-outlined">toc</span>
              <a href="#mission">Order</a>
            </li>

            <li>
              <span className="material-icons-outlined">payments</span>
              <a href="#mission">Invoices</a>
            </li>

            <li>
              <span className="material-icons-outlined">class</span>
              <a href="#mission">Bookings</a>
            </li>

            <li>
              <span className="material-icons-outlined">meeting_room</span>
              <a href="#mission">Appointments</a>
            </li>

            <li>
              <span className="material-icons-outlined">people</span>
              <a href="/dashboard/users">Users</a>
            </li>

            <li>
              <span className="material-icons-outlined">diversity_2</span>
              <a href="/dashboard/roles">Roles</a>
            </li>

            <li>
              <span className="material-icons-outlined">token</span>
              <a href="/dashboard/resource">Permissions</a>
            </li>

            <li>
              <span className="material-icons-outlined">manage_accounts</span>
              <a href="/dashboard/permissions">Role Permissions</a>
            </li>
          </>
        )}
      </ul>
    </aside>
  );
}
