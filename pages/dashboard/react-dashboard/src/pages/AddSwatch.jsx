import MyForm from "./MyForm";

export default function AddSwatch() {
  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h3 className="page-title"> Add New Swatch </h3>
        </div>

        <div className="wrapper">
          <MyForm />
        </div>
      </main>
    </>
  );
}
