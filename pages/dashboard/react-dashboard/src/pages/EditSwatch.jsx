import { useParams } from "react-router-dom";
import EditSwatchForm from "../components/EditForm";

export default function EditSwatch() {
  const { id } = useParams();

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper bg-white">
          <h3 className="page-title"> Edit Swatch </h3>
        </div>

        <div className="wrapper">
          <EditSwatchForm swatchId={id} />
        </div>
      </main>
    </>
  );
}
