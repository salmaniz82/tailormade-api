import Sidebar from "./Sidebar.jsx";
import Header from "./Header.jsx";
import { Outlet } from "react-router-dom";

function Dashboard() {
  const message = "this is from the reactJS";

  return (
    <>
      <section className="hbox stretch">
        <Sidebar />

        <section className="content">
          <section className="vbox">
            <Header />
            <section className="scrollable wrapper bg-white_offset">
              <Outlet />
            </section>
          </section>
        </section>
      </section>
    </>
  );
}

export default Dashboard;
