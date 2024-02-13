import { useRef, useState } from "react";
import { SlClose, SlEye, SlList, SlNote, SlTrash, SlPlus } from "react-icons/sl";
import { API_BASE_URL } from "../utils/helpers";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

export default function AddStock({ onCancelAdd, onNewStockAdd }) {
  const [metaKeys, setMetaKeys] = useState([]);
  const [formError, setFormError] = useState([]);

  const addStockFomRef = useRef();
  const titleRef = useRef();
  const urlRef = useRef();
  const aliasRef = useRef();
  const metaKeyRef = useRef();

  function isValidDomainName(domain) {
    const domainPattern = /^(?:(?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/;
    return domainPattern.test(domain);
  }

  const addManager = () => {
    const newMetaKey = metaKeyRef.current.value.trim();

    if (newMetaKey !== "" && !metaKeys.includes(newMetaKey)) {
      setMetaKeys((oldKeys) => [...oldKeys, newMetaKey]);
      metaKeyRef.current.value = "";
    }
  };

  const addMetakey = (e) => {
    if (e.key === "Enter") {
      addManager();
    }
  };

  const handleRemoveMetaKey = (metaItem, metaIndex) => {
    console.log(metaItem);
    setMetaKeys((oldMetakeys) => {
      return oldMetakeys.filter((item) => item != metaItem);
    });
  };

  const validateAddForm = () => {
    console.log("form validation in place");

    const error = [];

    if (titleRef.current.value == "") {
      error.push("Title is empty");
    }

    if (urlRef.current.value == "") {
      error.push("url is empty");
    }

    if (urlRef.current.value != "" && !isValidDomainName(urlRef.current.value)) {
      error.push("not valid domain url");
    }

    if (aliasRef.current.value == "") {
      error.push("alias is empty");
    }

    if (metaKeys.length == 0) {
      error.push("please add meta keys");
    }

    if (error.length > 0) {
      setFormError(error);
    } else {
      setFormError([]);
      handleSubmitRequest();
    }
  };

  const handleSubmitRequest = () => {
    var payload = {
      title: titleRef.current.value,
      url: urlRef.current.value,
      alias: aliasRef.current.value,
      metaFields: metaKeys
    };

    (async () => {
      console.log("async send is process");

      try {
        const request = await fetch(`${API_BASE_URL}stocks`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(payload)
        });

        if (!request.ok) {
          const errorData = await request.json();
          throw new Error(errorData.message);
        }

        const data = await request.json();
        onNewStockAdd(data.newStock);
      } catch (error) {
        toast.error(error.message);
      }
    })();
  };

  return (
    <div className="addNewStockWrapper bg-white mt-10 p-10">
      <div className="dfx-grid justify-space-between">
        <h4 className="text-md">Add New Stock</h4>
        <SlClose className="delete-icon r-icons" onClick={onCancelAdd} />
      </div>

      {formError.length > 0 &&
        formError.map((errorItem, errorIndex) => (
          <div key={errorIndex} className="errorLabels">
            {errorItem}
          </div>
        ))}

      <form id="add-swatch-form" className="addStockForm" ref={addStockFomRef}>
        <div className="dfx metaauto-fields">
          <label htmlFor="stockTitle">Title</label>
          <input type="text" placeholder="title" id="stockTitle" ref={titleRef} />
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="stockUrl">URL</label>
          <input type="text" placeholder="url" id="stockUrl" ref={urlRef} />
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="stockAlias">Alias</label>
          <input type="text" placeholder="Alias" id="stockAlias" ref={aliasRef} />
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields addStockMetaKeys-WRap">
          <label htmlFor="metaKeys">Meta Key</label>
          <input type="text" placeholder="Meta Key" id="MetaKey" ref={metaKeyRef} onKeyDown={(e) => addMetakey(e)} />
          <SlPlus className="edit-icon" onClick={(e) => addManager()} />
        </div>

        {metaKeys.length > 0 &&
          metaKeys.map((metaItem, metaIndex) => (
            <div className="dfx-grid justify-space-between" key={metaIndex}>
              <div>- {metaItem}</div>
              <div>
                <SlClose className="delete-icon r-icons" onClick={() => handleRemoveMetaKey(metaItem, metaIndex)} />
              </div>
            </div>
          ))}

        <div className="flashButtonWrapper mx-auto max-w-300">
          <div className="text_btn_lg" onClick={validateAddForm}>
            SAVE
          </div>
        </div>
      </form>

      <ToastContainer />
    </div>
  );
}
