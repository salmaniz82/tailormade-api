import { useEffect, useState } from "react";
import { API_BASE_URL } from "../utils/helpers";
import { SlRefresh, SlNote, SlTrash } from "react-icons/sl";
import AddStock from "../components/AddStock";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import EditStock from "../components/EditStock";

export default function Stocks() {
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

  const handleDelete = (itemId) => {
    console.log("delete stock with id of ", itemId);

    (async () => {
      try {
        const request = await fetch(`${API_BASE_URL}stocks/${itemId}`, {
          method: "DELETE"
        });

        const reponseData = await request.json();

        if (!request.ok) {
          throw new Error(reponseData.message);
        } else {
          setStockData((oldStock) => {
            return oldStock.filter((item) => item.id != itemId);
          });
        }
      } catch (error) {
        console.log("error", error.message);
      }
    })();
  };

  const handleFilterUpdate = (sourceUrl) => {
    (async () => {
      try {
        const request = await fetch(`${API_BASE_URL}filters?source=${sourceUrl}`);

        if (!request.ok) {
          const errorData = await request.json();
          throw new Error(errorData.message);
        }

        const successData = await request.json();

        toast.success(successData.message);
      } catch (error) {
        toast.error(error.message);
      }
    })();
  };

  const syncUpdatedRecord = (updatedRecord) => {
    let updatedID = updatedRecord.id;

    setStockData((existingStock) => {
      return existingStock.map((item) => {
        return item.id === updatedID ? updatedRecord : item;
      });
    });
  };

  const [editActive, setEditActive] = useState(false);
  const [editItem, setEditItem] = useState(false);

  const activateEdit = (item) => {
    setEditItem(item);
    setEditActive(true);
  };

  const clearEdit = () => {
    setEditActive(false);
    setEditItem(false);
  };

  useEffect(() => {
    fetchStock();
  }, []);

  return (
    <>
      <main className="dashboard-content_wrap">
        <div className="wrapper">
          <div className="dfx-grid">
            <h3 className="page-title bg-white flex-basis-70"> STOCKS </h3>
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
                  <th>&nbsp;</th>
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
                        <SlRefresh className="green-icon" onClick={() => handleFilterUpdate(item.url)} />
                      </td>

                      <td>
                        <SlNote className="edit-icon" onClick={() => activateEdit(item)} />
                      </td>
                      <td>
                        <SlTrash className="delete-icon" onClick={() => handleDelete(item.id)} />
                      </td>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
          {modeAddNew && <AddStock onCancelAdd={closeAdd} onNewStockAdd={newStockHandler} />}
          {editActive && <EditStock clearEdit={clearEdit} onRecordUpdated={syncUpdatedRecord} activeEditItem={editItem} />}
        </div>
        <ToastContainer />
      </main>
    </>
  );
}
