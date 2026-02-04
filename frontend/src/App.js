import "./App.css";
import Header from "./Component/Layout/Header";
import Footer from "./Component/Layout/Footer";
import MenuLeft from "./Component/Layout/MenuLeft";
import MenuAcc from "./Component/Layout/MenuAcc";
import { useLocation } from "react-router-dom";

import { Provider } from "react-redux";
import { store } from "./Redux/store";

function App(props) {
  const params1 = useLocation();
  const path = params1.pathname;

  const showMenu = !path.includes("cart");

  return (
    <Provider store={store}>
      <Header />

      <section>
        <div className="container">
          <div className="row">
            {showMenu && (path.includes("account") ? <MenuAcc /> : <MenuLeft />)}

            {props.children}
          </div>
        </div>
      </section>

      <Footer />
    </Provider>
  );
}

export default App;
