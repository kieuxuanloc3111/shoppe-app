import logo from './logo.svg';
import './App.css';
import Header from "./Component/Layout/Header";
import Footer from "./Component/Layout/Footer";
import MenuLeft from "./Component/Layout/MenuLeft";
import { useLocation } from 'react-router-dom';
import MenuAcc from './Component/Layout/MenuAcc';
import { CartProvider } from "./Context/CartContext";

function App(props) {
  let params1 = useLocation();
  const path = params1.pathname; 

  const showMenu =
    !path.includes("cart"); 

  return (
    <CartProvider>
      <Header />

      <section>
        <div className="container">
          <div className="row">

            {showMenu && (
              path.includes("account") ? <MenuAcc /> : <MenuLeft />
            )}

            {props.children}
          </div>
        </div>
      </section>

      <Footer />
    </CartProvider>
  );
}

export default App;

// b
