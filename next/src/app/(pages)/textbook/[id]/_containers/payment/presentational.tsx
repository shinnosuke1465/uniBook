"use client";

import { useEffect } from "react";
import { loadStripe } from "@stripe/stripe-js";
import {
  Elements,
  PaymentElement,
  useStripe,
  useElements,
} from "@stripe/react-stripe-js";

const stripePromise = loadStripe(
  process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!
);

interface PaymentPresentationProps {
  clientSecret: string | null;
  error: string | null;
  price: number;
}

export function PaymentPresentation({
  clientSecret,
  error,
  price,
}: PaymentPresentationProps) {
  if (error) {
    return (
      <div className="rounded-lg bg-red-50 p-4">
        <p className="text-sm text-red-600">{error}</p>
      </div>
    );
  }

  if (!clientSecret) {
    return (
      <div className="flex items-center justify-center py-8">
        <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"></div>
        <span className="ml-3 text-gray-600">読み込み中...</span>
      </div>
    );
  }

  return (
    <Elements
      stripe={stripePromise}
      options={{
        clientSecret,
        appearance: {
          theme: "stripe",
          variables: {
            colorPrimary: "#2563eb",
          },
        },
      }}
    >
      <CheckoutForm price={price} />
    </Elements>
  );
}

function CheckoutForm({ price }: { price: number }) {
  const stripe = useStripe();
  const elements = useElements();

  return (
    <div className="space-y-6">
      <div className="rounded-lg border border-gray-200 p-6">
        <h3 className="mb-4 text-lg font-semibold">支払い情報</h3>

        <div className="mb-4">
          <div className="flex items-center justify-between rounded-lg bg-gray-50 p-4">
            <span className="text-sm text-gray-600">支払い金額</span>
            <span className="text-2xl font-bold text-blue-600">
              ¥{price.toLocaleString()}
            </span>
          </div>
        </div>

        <PaymentElement
          options={{
            layout: "tabs",
            paymentMethodOrder: ["card"],
          }}
        />
      </div>

      <p className="text-center text-xs text-gray-500">
        支払い情報は安全に暗号化されて送信されます
      </p>
    </div>
  );
}