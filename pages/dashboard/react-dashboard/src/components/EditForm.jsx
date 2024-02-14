import React, { useState, useEffect, useRef } from "react";
import { API_BASE_URL, convertArrayToObject } from "../utils/helpers";
import Select from "react-select";
import { SlClose, SlPlus, SlArrowLeftCircle, SlArrowRightCircle } from "react-icons/sl";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

import { useNavigate } from "react-router-dom";

function EditSwatchForm({ swatchId }) {
  const Navigate = useNavigate();
  const [loading, setLoading] = useState(true);

  const [getMetaData, setMetaData] = useState([]);
  const [selectedOption, setSelectedOption] = useState(null);
  const [defaultSelect, setDefaultSelect] = useState(null);
  const [editFormData, setEditFormData] = useState(null);
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

  const [options, setOptions] = useState([]);

  const fetchMetaData = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}swatchemeta`);
      const responseData = await response.json();

      if (responseData.metadata !== undefined && response.ok) {
        setMetaData(responseData.metadata);

        let preparedOPtions = responseData.metadata.map((meta) => ({
          value: meta.url,
          label: meta.title,
          metaFields: meta.metaFields
        }));
        setOptions((oldvalues) => preparedOPtions);
      }
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  };

  const fetchSwatch = async () => {
    try {
      const response = await fetch(`${API_BASE_URL}swatch/${swatchId}`);

      const resJson = await response.json();

      if (!response.ok) {
        console.log("respons is ok");
        const errorBody = resJson.message;

        throw new Error(errorBody);
      } else {
        let swatchData = resJson.swatch;
        setEditFormData(swatchData);

        let rawObjectMeta = JSON.parse(swatchData.productMeta);
        let formattedObjectMeta = Object.entries(rawObjectMeta).map(([key, value]) => ({ key, value }));
        setFormData(formattedObjectMeta);

        let swatchSource = swatchData.source;

        setTimeout(() => {
          console.log("options timeout", options);
        }, 500);

        /*
        const defaultOption = options.find((option) => option.value == swatchSource);
        delete defaultOption.metaFields;
        setDefaultSelect(defaultOption);
        console.log("hello say ", defaultOption);       
        */

        /*
        const result = await prepareOptions(swatchSource);
        */

        console.log("what to say");
      }
    } catch (error) {}
  };

  const prepareOptions = (currentSource) => {
    console.log(options);
    return new Promise((resolve, reject) => {
      const defaultOption = options.find((option) => option.value == currentSource);

      delete defaultOption.metaFields;

      if (defaultOption) {
        setDefaultSelect([defaultOption]);
        return resolve(defaultOption);
      }

      return reject("unmatched entitity");
    });
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
    console.log("sending request with", payload);

    try {
      const response = await fetch(`${API_BASE_URL}swatches/${swatchId}`, {
        method: "PUT",
        body: JSON.stringify(payload),
        headers: {
          "Content-Type": "application/json"
        }
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
    /*
    formRef.current.reset();
    setSelectedImage(null);
    */
  };

  const handleSubmit = () => {
    let productMeta = convertArrayToObject(formData);

    let formPayload = {
      title: titleRef.current.value,
      source: stockRef.current.value || editFormData.source,
      productMeta: productMeta,
      operation: "content-update"
    };

    sendSaveRequest(formPayload);
  };

  useEffect(() => {
    fetchMetaData();
    fetchSwatch();
  }, [swatchId]);

  const handleTitleUpdate = (inputValue) => {
    console.log("title value", inputValue);

    setEditFormData((oldValue) => {
      return {
        ...oldValue,
        title: inputValue
      };
    });
  };

  useEffect(() => {
    // Call prepareOptions whenever options state changes
    if (options.length > 0 && editFormData) {
      prepareOptions(editFormData.source)
        .then((result) => {
          console.log(result);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  }, [options, editFormData]);

  const handleNavigation = (id, direction) => {
    console.log(id, direction);

    const swatchId = direction == "next" ? (id += 1) : (id -= 1);

    Navigate(`/editswatch/${swatchId}`);
  };

  return (
    editFormData && (
      <form name="add-swatch-form" id="add-swatch-form" encType="multipart/form-data" method="post" className="bg-white mx-auto" ref={formRef}>
        <div className="dfx">
          <div className="dfx metaauto-fields">
            <label htmlFor="imgDFxPreview">
              <SlArrowLeftCircle className="edit-icon r-icon" onClick={() => handleNavigation(editFormData.id, "prev")} />
            </label>

            <div className="addingSwatch ImagePreview" id="imgDFxPreview">
              {true && (
                <div>
                  <img src={`${API_BASE_URL}/${editFormData.thumbnail}`} alt="Selected" />
                </div>
              )}
            </div>

            <div>
              <SlArrowRightCircle className="edit-icon r-icon" onClick={() => handleNavigation(editFormData.id, "next")} />
            </div>
          </div>

          {false && (
            <div className="dfx metaauto-fields">
              <label htmlFor="image">Image:</label>

              <div>
                <input type="file" id="image" accept="image/*" onChange={handleImageChange} />
              </div>
            </div>
          )}

          <div className="dfx metaauto-fields">
            <label htmlFor="title">Title:</label>
            <input type="text" name="title" id="title" placeholder="Title" ref={titleRef} value={editFormData.title} onChange={(e) => handleTitleUpdate(e.target.value)} />
            <div>&nbsp;</div>
          </div>

          <div className="dfx metaauto-fields">
            <label htmlFor="stock-select">STOCK COLLECTION</label>
            <Select options={options} onChange={handleSelectChange} placeholder="Choose Stock Collection" id="stock-select" ref={stockRef} value={defaultSelect} />
            <div> &nbsp; </div>
          </div>

          {formData && (
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
            UPDATE
          </div>
        </div>

        <ToastContainer />
      </form>
    )
  );
}

export default EditSwatchForm;
