class A2
  def self.hi(language)
    translator = Translator.new(language)
    translator.hi
  end
end

require 'a2/translator'
