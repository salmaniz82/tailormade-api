import { useEffect, useState } from "react";
import { API_BASE_URL } from "../utils/helpers";
import { SlClose, SlEye, SlList, SlNote, SlTrash } from "react-icons/sl";
import AddStock from "../components/AddStock";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

export default function Collections() {
  const [loading, setLoading] = useState(true);
  const [stockData, setStockData] = useState(null);

  const [modeAddNew, setModeAddNew] = useState(false);

  const fetchStock = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}stocks`);
      const responseData = await response.json();

      if (response.ok) {
        setStockData(responseData.stocks);
      } else {
        throw new Error(responseData.message);
      }
    } catch (error) {
      console.log("got error while fetch stock");
    }
  };

  const closeAdd = () => {
    setModeAddNew(false);
  };

  const newStockHandler = (newStock) => {
    setStockData((oldStocks) => [...oldStocks, newStock]);
    closeAdd();
  };

  useEffect(() => {
    fetchStock();
  }, []);

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper">
          <div className="dfx-grid">
            <h3 className="page-title bg-white flex-basis-70"> Collections </h3>
            <div className="flex-basis-30 bg-white">
              {!modeAddNew && (
                <div className="flashButtonWrapper mx-auto max-w-300">
                  <div className="text_btn_lg" onClick={() => setModeAddNew(true)}>
                    ADD NEW
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="dfx-grid">
          <div className="wrapper mt-10">
            <table className="bg-white stock-table" valign="top">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>URL</th>
                  <th>Alias</th>
                  <th>Meta Keys</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>

              <tbody>
                {stockData &&
                  stockData.map((item) => (
                    <tr key={item.id}>
                      <td>{item.id}</td>
                      <td>{item.title}</td>
                      <td>{item.url}</td>
                      <td>{item.alias}</td>

                      <td>
                        <ul className="stockkeyList">
                          {JSON.parse(item.metaFields).map((metaIndex, metaItem) => (
                            <li key={metaIndex}>- {metaIndex}</li>
                          ))}
                        </ul>
                      </td>

                      <td>
                        <SlNote className="edit-icon" />
                      </td>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
          {modeAddNew && <AddStock onCancelAdd={closeAdd} onNewStockAdd={newStockHandler} />}
        </div>
        <ToastContainer />
      </main>
    </>
  );
}
