import { useParams } from "react-router-dom";

export default function EditSwatch() {
  let { id } = useParams();

  console.log(id);

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h3 className="page-title"> Edit Swatch </h3>
        </div>

        <div className="wrapper">
          <div>Edit Swatch is here</div>
        </div>
      </main>
    </>
  );
}
