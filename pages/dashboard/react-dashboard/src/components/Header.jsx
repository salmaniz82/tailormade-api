export default function Header() {
  return (
    <header className="bg-black dk">
      <div className="d-flex justify-space-between align-center pt-10">
        <div className="dashPageTitle">
          <h4 className="t-white page-title"> Dashboard </h4>
        </div>
        <div className="dashRightArea d-flex align-end">
          <span className="material-icons-outlined t-white">person</span>
          <span className="t-white" style={{ marginLeft: "5px" }}>
            Admin
          </span>
        </div>
      </div>
    </header>
  );
}
