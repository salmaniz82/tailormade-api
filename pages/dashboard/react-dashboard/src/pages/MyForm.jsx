import React, { useState, useEffect, useRef } from "react";
import { API_BASE_URL, convertArrayToObject } from "../utils/helpers";
import Select from "react-select";
import { SlClose, SlEye, SlList, SlNote, SlTrash, SlPlus } from "react-icons/sl";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

function MyForm() {
  const [getMetaData, setMetaData] = useState([]);
  const [selectedOption, setSelectedOption] = useState(null);
  const [formData, setFormData] = useState([]);
  const [newKey, setNewKey] = useState("");
  const [newValue, setNewValue] = useState("");
  const [showAddFields, setShowAddFields] = useState(false);
  const [selectedImage, setSelectedImage] = useState(null);

  const [FormErro, setFormError] = useState(false);

  const titleRef = useRef();
  const statusRef = useRef();
  const stockRef = useRef();

  const formRef = useRef();

  const fetchMetaData = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}swatchemeta`);
      const responseData = await response.json();

      if (responseData.metadata !== undefined && response.ok) {
        setMetaData(responseData.metadata);
      }
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  };

  const handleSelectChange = (selectedOption) => {
    stockRef.current.value = selectedOption.value;

    const selectedMetaFields = JSON.parse(selectedOption.metaFields);

    const initialFormData = selectedMetaFields.map((field) => ({
      key: field,
      value: ""
    }));

    setFormData(initialFormData);
    setSelectedOption(selectedOption);
  };

  const handleInputChange = (index, value) => {
    setFormData((prevFormData) => {
      const updatedFormData = [...prevFormData];
      updatedFormData[index].value = value;
      return updatedFormData;
    });
  };

  const handleRemoveField = (index) => {
    setFormData((prevFormData) => {
      const updatedFormData = [...prevFormData];
      updatedFormData.splice(index, 1);
      return updatedFormData;
    });
  };

  const handleAddField = () => {
    if (newKey && newValue) {
      setFormData((prevFormData) => [...prevFormData, { key: newKey, value: newValue }]);
      setNewKey("");
      setNewValue("");
    }
  };

  const handleImageChange = (event) => {
    const file = event.target.files[0];
    setSelectedImage(file);
  };

  async function sendSaveRequest(payload) {
    try {
      const response = await fetch(`${API_BASE_URL}swatches`, {
        method: "POST",
        body: payload
      });

      const data = await response.json();

      if (data.code == 200) {
        console.log("success do action");
        toast.success(data.message);
        handleFormReset();
      } else {
        toast.error(data.message);
        console.log("do error action");
      }
    } catch (error) {
      console.error(error);
    }
  }

  const handleFormReset = () => {
    formRef.current.reset();
    setSelectedImage(null);
  };

  const handleSubmit = () => {
    console.log("status", titleRef.current.value);
    console.log("status", statusRef.current.value);
    console.log("stock select", stockRef.current.value);

    let productMeta = convertArrayToObject(formData);

    // Create a FormData object
    const formPayload = new FormData();

    // Append form data
    formPayload.append("title", titleRef.current.value);
    formPayload.append("status", statusRef.current.value);
    formPayload.append("source", stockRef.current.value);
    formPayload.append("productMeta", JSON.stringify(productMeta));

    // Append the file
    formPayload.append("file", selectedImage);
    sendSaveRequest(formPayload);
  };

  const options = getMetaData.map((meta) => ({
    value: meta.url,
    label: meta.title,
    metaFields: meta.metaFields
  }));

  useEffect(() => {
    fetchMetaData();
  }, []);

  return (
    <form name="add-swatch-form" id="add-swatch-form" enctype="multipart/form-data" method="post" className="bg-white mx-auto" ref={formRef}>
      <div className="dfx">
        <div className="dfx metaauto-fields">
          <label>Status:</label>
          <div>
            <label className="switch" htmlFor="status">
              <input type="checkbox" className="user-check-toggle" id="status" value="1" ref={statusRef} defaultChecked={true} />
              <div className="slider round"></div>
            </label>
          </div>
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="imgDFxPreview">Image Selected</label>

          <div className="addingSwatch ImagePreview" id="imgDFxPreview">
            {selectedImage && (
              <div>
                <img src={URL.createObjectURL(selectedImage)} alt="Selected" />
              </div>
            )}
          </div>

          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="image">Image:</label>

          <div>
            <input type="file" id="image" accept="image/*" onChange={handleImageChange} />
          </div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="title">Title:</label>
          <input type="text" name="title" id="title" placeholder="Title" ref={titleRef} />
          <div>&nbsp;</div>
        </div>

        <div className="dfx metaauto-fields">
          <label htmlFor="stock-select">STOCK COLLECTION</label>
          <Select options={options} onChange={handleSelectChange} placeholder="Choose Stock Collection" id="stock-select" ref={stockRef} />
          <div>&nbsp;</div>
        </div>

        {selectedOption && (
          <div>
            {formData.map((field, index) => (
              <div key={field.key} className="dfx metaauto-fields">
                <label>{field.key}:</label>
                <input type="text" placeholder={field.key} value={field.value} onChange={(e) => handleInputChange(index, e.target.value)} />
                <SlClose className="delete-icon r-icons" onClick={() => handleRemoveField(index)} />
              </div>
            ))}

            <div>
              <p>Enter key / value and press (+) icon to register new meta fields</p>
            </div>

            <div className="dfx metaauto-fields">
              <input type="text" placeholder="Key" value={newKey} onChange={(e) => setNewKey(e.target.value)} />
              <input type="text" placeholder="Value" value={newValue} onChange={(e) => setNewValue(e.target.value)} />
              <SlPlus className="edit-icon r-icons" onClick={handleAddField} />
            </div>
          </div>
        )}
      </div>

      <div className="flashButtonWrapper mx-auto">
        <div className="text_btn_lg" onClick={handleSubmit}>
          SAVE
        </div>
      </div>

      <ToastContainer />
    </form>
  );
}

export default MyForm;
