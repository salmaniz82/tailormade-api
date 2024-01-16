export default function NotFound() {
  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h1 className="page-title" style={{ color: "red" }}>
            404{" "}
          </h1>
          <p className="text-bold">Request not found</p>
        </div>
      </main>
    </>
  );
}
