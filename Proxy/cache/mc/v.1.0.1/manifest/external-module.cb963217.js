import { initializeApp } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app.js";
import {
  getAuth,
  RecaptchaVerifier,
  signInWithPhoneNumber,
} from "https://www.gstatic.com/firebasejs/9.14.0/firebase-auth.js";
import qrious from "https://cdn.jsdelivr.net/npm/qrious@4.0.2/+esm";

window.tcg.mc._firebase = {
  getAuth,
  initializeApp,
  RecaptchaVerifier,
  signInWithPhoneNumber,
};

window.QRious = qrious;
