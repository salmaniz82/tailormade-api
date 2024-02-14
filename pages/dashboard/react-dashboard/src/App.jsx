import { RouterProvider, createBrowserRouter, Route, createRoutesFromElements } from "react-router-dom";

import "./App.css";
import Dashboard from "./components/Dashbvoard";
import Swatches from "./pages/Swatches.jsx";
import Demo from "./pages/Demo.jsx";
import NotFound from "./pages/NotFound.jsx";
import AddSwatch from "./pages/AddSwatch.jsx";
import EditSwatch from "./pages/EditSwatch.jsx";
import Stocks from "./pages/Stocks.jsx";

function App() {
  const message = "this is from the reactJS";

  const router = createBrowserRouter(
    createRoutesFromElements(
      <Route>
        <Route element={<Dashboard />}>
          <Route index path="/" element={<Swatches />} />
          <Route path="/demo" element={<Demo />} />
          <Route path="/addswatch" element={<AddSwatch />} />
          <Route path="/editswatch/:id" element={<EditSwatch />} />
          <Route path="/stocks" element={<Stocks />} />
          <Route path="*" element={<NotFound />} />
        </Route>
      </Route>
    ),
    { basename: "/dashboard" }
  );

  return (
    <>
      <RouterProvider router={router} />
    </>
  );
}

export default App;
